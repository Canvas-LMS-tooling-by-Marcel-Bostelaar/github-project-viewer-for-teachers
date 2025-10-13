<?php
namespace CanvasApiLibrary\Providers\Utility;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Services\CanvasCommunicator;
use CanvasApiLibrary\Services\StatusHandlerInterface;
use CanvasApiLibrary\Models\Domain;

abstract class AbstractProvider{
    public function __construct(
        public readonly StatusHandlerInterface $statusHandler,
        public readonly CanvasCommunicator $canvasCommunicator
        ){}

    /**
     * Some services can prefetch additional data with requests, such as an assignment which can prefetch users.
     * If additional data is fetched, the fetched info is passed to their corresponding providers "handlEmitted" method.
     * This can be overridden to do things such as caching.
     * @param mixed $data A single raw data item to be processed and handled.
     * @return void 
     */
    public function HandleEmitted(mixed $data, Domain $domain){
        //Do nothing by default.
    }

    /**
     * Maps data
     * @param mixed $data
     * @return \CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel[]
     */
    protected abstract function MapData(mixed $data, Domain $domain, array $suplementaryDataMapping): array;

    protected function Get(Domain $domain, string $route, array $suplementaryDataMapping = []): array{
        [$data, $status] = $this->canvasCommunicator->Get($route, $domain);
        $data = $this->statusHandler->HandleStatus($data, $status);
        return $this->MapData($data, $domain, $suplementaryDataMapping);
    }

    /**
     * Provides array based methods for any provider calls. Used the class name and provided plural name(s) to find the corresponding methods for a singular object.
     * For example, getCommentsForAssignment(domain, course, assignment) exists, which returns an Assignment, which has a plural form Assignments. 
     * This then automagically creates getCommentForAssignments(domain, course, array assignment) : Lookup<Assignment, Comment>
     * Method signature is identical, except for replacing the singular argument with an array of the models.
     * If multiple plurals are provided, all are valid. For example, cacti, cactuses -> getSpikesForCacti and getSpikesForCactuses both work.
     * @param mixed $method
     * @param mixed $args
     * @throws \BadMethodCallException
     * @return Lookup
     */
    public function __call($method, $args){
        $indexOfMultiItem = -1;
        $foundPluralName = null;
        for($i = 0; $i < count($args); $i++){
            if(is_array($args[$i]) && count($args[$i]) > 0 && is_subclass_of($args[$i][0], ModelInterface::class)){
                $plurals = $args[$i][0]::getPluralNames();
                foreach($plurals as $plural){
                    //check if method ends with this plural
                    if(str_ends_with(strtolower($method), strtolower($plural))){
                        if($indexOfMultiItem !== -1){
                            throw new \BadMethodCallException("Multiple array arguments detected in call to $method. Only one array of models is allowed.");
                        }
                        $indexOfMultiItem = $i;
                        $foundPluralName = $plural;
                    }
                }
            }
        }
        if($indexOfMultiItem === -1){
            throw new \BadMethodCallException("Unknown method: $method.");
        }
        $singularName = $args[$indexOfMultiItem][0]::class;
        $fixedMethodName = substr($method, -strlen($foundPluralName), strlen($foundPluralName)) . $singularName;

        if(!method_exists($this, $fixedMethodName)){
            throw new \BadMethodCallException("Unknown method: $method. No corresponding method $fixedMethodName found.");
        }

        $headArgs = array_slice($args, 0, $indexOfMultiItem);
        $tailArgs = array_slice($args, $indexOfMultiItem + 1);
        $newLookup = new Lookup();
        try{
            foreach($args[$indexOfMultiItem] as $item){
                if(!is_a($item, $singularName, true)){
                    throw new \BadMethodCallException("Array passed to $method contains an item that is not of type $singularName: " . serialize($item) . ".");
                }
                $newLookup->add($item, 
                    call_user_func([$this, $fixedMethodName], ...array_merge($headArgs, [$item], $tailArgs))
                );
            }
        }catch(\ArgumentCountError $e){
            throw new \BadMethodCallException("Argument count mismatch when calling $fixedMethodName. Check that the method signature matches the arguments passed to $method, and has the correct array in the same position.", 0, $e);
        }
        catch(\TypeError $e){
            throw new \BadMethodCallException("Type error when calling $fixedMethodName. Check that the method signature matches the arguments passed to $method, and has the correct array in the same position.", 0, $e);
        }
        return $newLookup;
    }
}