<?php

namespace CanvasApiLibrary\Models;

/**
 * @property string $name
 */
class Student extends BaseModel{
    protected static array $properties = [
        ["string", "name"]
    ];
}