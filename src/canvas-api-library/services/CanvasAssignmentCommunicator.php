<?php

namespace CanvasApiLibrary\Services;

use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;

class CanvasAssignmentCommunicator extends AbstractCanvasCommunicator{
    public function __construct(string $baseURL, string $apiKey, public readonly Course $course, public readonly Assignment $assignment){
        parent::__construct($baseURL, $apiKey);
        $this->baseURL = "$baseURL/courses/$course->id/assignments/$assignment->id";
    }
    public function Get(string $route) : array{
        return self::curlGet($this->baseURL . $route, $this->apiKey);
    }

    public function Put(string $route, mixed $data) : array {
        return self::curlPut($this->baseURL . $route, $this->apiKey, $data);
    }
}