<?php

namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Submission;
use CanvasApiLibrary\Providers\StudentProvider;
use CanvasApiLibrary\Services\CanvasAssignmentCommunicator;


class SubmissionProvider extends AbstractProvider{

    protected function MapData(mixed $data, array $suplementaryDataMapping = []): array{
        return array_map_to_models($data, Submission::class, [
            "url", 
            ["submittedAt", fn($v) => $v ? new \DateTime($v) : null],
            ["assignment", "assignment_id", fn($v) => new Models\Assignment($v)],
            ["student", "user_id", fn($v) => new Models\Student($v)],
            ...$suplementaryDataMapping
        ]);
    }

    /**
     * Summary of getSubmissionsForAssignment
     * @param \CanvasApiLibrary\Services\CanvasAssignmentCommunicator $communicator
     * @param mixed $studentProvider Optional, if provided will be used to pre-fetch student info and emit to student provider
     * @return Submission[]
     */
    protected function getSubmissionsForAssignment(CanvasAssignmentCommunicator $communicator, ?StudentProvider $studentProvider = null) : array{
        $postfix = $studentProvider ? "?include[]=user" : "";
        // $url = "$this->assignmentURL/submissions?include[]=group&include[]=user&per_page=100";
        return $this->Get($communicator,
            "/submissions$postfix",
            $studentProvider ? [["user", $studentProvider]] : []
        );
    }
}