<?php
namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

class GroupCategory extends AbstractCanvasPopulatedModel{
    public static function getPluralNames(): array{
        return ["GroupCategories"];
    }
}