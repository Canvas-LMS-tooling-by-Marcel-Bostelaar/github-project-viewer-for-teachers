<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

/**
 * @property string $name
 */
class Group extends AbstractCanvasPopulatedModel{
    protected static array $properties = [["string", "name"]];

    public static function getPluralNames(): array{
        return ["Groups"];
    }
}
