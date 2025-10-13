<?php

namespace CanvasApiLibrary\Models;

use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

class Assignment extends AbstractCanvasPopulatedModel{
    public static function getPluralNames(): array{
        return ["Assignments"];
    }
}