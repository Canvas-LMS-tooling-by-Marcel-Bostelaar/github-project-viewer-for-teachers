<?php

namespace CanvasApiLibrary\Models;

class Section extends BaseModel{
    protected static array $properties = [
        ["string", "name"],
        [Course::class, "course"]
    ];
}