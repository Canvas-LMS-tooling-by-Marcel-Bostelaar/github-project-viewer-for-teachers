<?php

namespace CanvasApiLibrary\Models;

/**
 * @property string $feedbackGiver
 * @property string $comment
 * @property \DateTime $date
 */
class SubmissionFeedback extends BaseModel{
    protected static array $properties = [
        ["string", "feedbackGiver"],
        ["string", "comment"],
        [\DateTime::class, "date"]
    ];
}