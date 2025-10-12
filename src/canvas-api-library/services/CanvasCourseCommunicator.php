<?php

namespace CanvasApiLibrary\Services;

use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;

class CanvasCourseCommunicator extends AbstractCanvasCommunicator{
    public function __construct(string $baseURL, string $apiKey, public readonly Course $course){
        parent::__construct($baseURL, $apiKey);
        $this->baseURL = "$baseURL/courses/$course->id";
    }
}