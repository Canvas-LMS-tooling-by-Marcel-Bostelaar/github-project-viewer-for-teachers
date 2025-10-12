<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Exceptions\NotPopulatedException;

abstract class BaseModel{

    public readonly int $id;
    /**
     * A list of property names to be dynamically generated/handled. 
     * Optionally, instead of a property name, a [type, name] can be given, which will be used for type checking on setting.
     * If an unpopulated property is accessed, a NotPopulatedException is thrown.
     * List is used to calculate whether or not a model is fully populated.
     * Use docstring to provide info about properties to tooling.
     * @var array<string|array>
     */
    protected static array $properties = [];
    private array $virtualProperties = [];

    public function __get($name) {
        if (!array_key_exists($name, $this->virtualProperties)){
            $trace = debug_backtrace();
            $classname = get_class($this);
            trigger_error(
                "Undefined property on model '$classname' via __get(): " . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
        if($this->virtualProperties[$name]['value'] === null){
            throw new NotPopulatedException("Property $name has not been populated yet. Please populate the model first.");
        }
        return $this->virtualProperties[$name]['value'];
    }

    public function __set($name, $value) {
        if($value === null){
            throw new \InvalidArgumentException("Property $name cannot be set to null.");
        }
        if (!array_key_exists($name, $this->virtualProperties)){
            $trace = debug_backtrace();
            $classname = get_class($this);
            trigger_error(
                "Undefined property on model '$classname' via __set(): " . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
        $type = $this->virtualProperties[$name]['type'];
        if($type !== null){
            if(!self::isA($value, $type)){
                throw new \InvalidArgumentException("Property $name must be of type $type, " . gettype($value) . " given.");
            }
        }
        $this->virtualProperties[$name]['value'] = $value;
    }

    /**
     * Used to check type equality, also when using primitives
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    private static function isA($value, string $type): bool {
        return match ($type) {
            'int' => is_int($value),
            'string' => is_string($value),
            'array' => is_array($value),
            'float' => is_float($value),
            'bool' => is_bool($value),
            'object' => is_object($value),
            default => $value instanceof $type,
        };
    }

    /**
     * Indicated whether or not the class is populated from the corresponding canvas system.
     * 
     * @var bool
     */
    public bool $isPopulated{
        get {
            foreach($this->virtualProperties as $_ => $info){
                if($info['value'] === null){
                    return false;
                }
            }
            return true;
        }
    }
    /**
     * Constructs a new basemodel. Do not override the constructor with non-optional parameters.
     * @param int $canvasID The canvas ID of the object.
     */
    protected function __construct(int $canvasID){
        $this->canvasID = $canvasID;

        //Set up virtual properties
        foreach($this->properties as $property){
            $type = null;
            $name = null;
            if(is_array($property)){
                if(count($property) !== 2){
                    throw new \InvalidArgumentException("Property definition with type must have exactly two elements: [type, name]");
                }
                $type = $property[0];
                $name = $property[1];
                if(!is_string($type) || !is_string($name)){
                    throw new \InvalidArgumentException("Property definitions must be either strings or [type, name] (both strings) arrays.");
                }
            } else {
                if(!is_string($property)){
                    throw new \InvalidArgumentException("Property definitions must be either strings or [type, name] arrays.");
                }
                $name = $property;
            }
            if(array_key_exists($name, $this->virtualProperties)){
                throw new \InvalidArgumentException("Property $name is defined multiple times in " . get_class($this) . ".");
            }
            $this->virtualProperties[$name] = [
                'type' => $type,
                'value' => null
            ];
        }
    }

    /**
     * Creates a non-populated ghost version of the model.
     * Use this for retrieving data based on an ID provided in a url, for example, which only needs the ID.
     * Non populated models can be returned by some endpoints as well.
     * @param mixed $canvasID
     * @return self
     */
    public static function createStub($canvasID): static{
        return new static($canvasID);
    }
}