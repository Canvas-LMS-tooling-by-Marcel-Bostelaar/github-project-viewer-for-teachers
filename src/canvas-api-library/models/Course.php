<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

class Course extends AbstractCanvasPopulatedModel{
    public static function getPluralNames(): array{
        return ["Courses"];
    }
}