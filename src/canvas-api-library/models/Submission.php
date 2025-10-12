<?php

namespace CanvasApiLibrary\Models;

/**
 * @property Student $student
 * @property Assignment $assignment
 * @property ?string $url
 * @property ?\DateTime $submittedAt
 */
class Submission extends BaseModel{
    protected static array $properties = [
        [Student::class, "student"],
        [Assignment::class, "assignment"]
    ];
    protected static array $nullableProperties = [
        ["string", "url"], 
        [\DateTime::class, "submittedAt"]
    ];
}