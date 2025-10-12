<?php

abstract class BaseModel{
    private int $canvasID;
    /**
     * Indicated whether or not the class it populated from the corresponding canvas system.
     * 
     * @var bool
     */
    private bool $isPopulated;
    /**
     * Constructs a new basemodel. Do not override the constructor with non-optional parameters.
     * @param int $canvasID The canvas ID of the object.
     */
    protected function __construct(int $canvasID){
        $this->canvasID = $canvasID;
    }

    public function getCanvasID(): int{
        return $this->canvasID;
    }
    
    /**
     * Returns a boolean indicating whether or not this model instance is populated with data.
     * @return bool
     */
    public function isPopulated(): bool{
        return $this->isPopulated;
    }

    /**
     * Creates a non-populated ghost version of the model.
     * Use this for retrieving data based on an ID provided in a url, for example.
     * @param mixed $canvasID
     * @return self
     */
    public static function createStub($canvasID): static{
        return new static($canvasID);
    }
}