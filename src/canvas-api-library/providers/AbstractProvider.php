<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Services\AbstractCanvasCommunicator;
use CanvasApiLibrary\Services\StatusHandlerInterface;

abstract class AbstractProvider{
    public function __construct(public readonly StatusHandlerInterface $statusHandler){}

    /**
     * Some services can prefetch additional data with requests, such as an assignment which can prefetch users.
     * If additional data is fetched, the fetched info is passed to their corresponding providers "handlEmitted" method.
     * This can be overridden to do things such as caching.
     * @param mixed $data A single raw data item to be processed and handled.
     * @return void 
     */
    public function HandleEmitted(mixed $data){
        //Do nothing by default.
    }

    /**
     * Maps data
     * @param mixed $data
     * @return \CanvasApiLibrary\Models\BaseModel[]
     */
    protected abstract function MapData(mixed $data, array $suplementaryDataMapping): array;

    protected function Get(AbstractCanvasCommunicator $communicator, string $route, array $suplementaryDataMapping = []): array{
        [$data, $status] = $communicator->Get($route);
        $data = $this->statusHandler->HandleStatus($data, $status);
        return $this->MapData($data, $suplementaryDataMapping);
    }

    //TODO implement dynamic method calls for multi-getters
    //Implement some magic for english pluralization? Or just require basemodels to define their plural forms?
    //Re-implement calls to always ask for the systemURL, courseid, assignment? Maybe make optional and fetch from a seperate helper if it doesnt exist?
    //Pass a context array that just has those keys for usability. Pass the actual communicator into the construct call of the provider.
}