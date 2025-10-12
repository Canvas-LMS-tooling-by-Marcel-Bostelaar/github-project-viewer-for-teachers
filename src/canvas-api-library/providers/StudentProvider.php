<?php
namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Student;
class StudentProvider extends AbstractProvider{
    public function __construct(
        public readonly Services\StatusHandlerInterface $statusHandler
    ){}

    /**
     * Summary of getStudentsInGroup
     * @param \CanvasApiLibrary\Models\Group $group
     * @param \CanvasApiLibrary\Services\CanvasGlobalCommunicator $communicator
     * @return Student[]
     */
    public function getStudentsInGroup(Models\Group $group, Services\CanvasGlobalCommunicator $communicator): array{
        return $this->Get($communicator,
        "/groups/{$group->id}/users");
    }

    protected function MapData(mixed $data, array $suplementaryDataMapping = []): array{
        return array_map_to_models($data, Student::class, ["name", ...$suplementaryDataMapping]);
    }
}