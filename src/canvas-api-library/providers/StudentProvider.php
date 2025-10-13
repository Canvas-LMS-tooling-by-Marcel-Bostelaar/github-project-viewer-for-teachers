<?php
namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Student;
use CanvasApiLibrary\Models\Domain;

/**
 * Provider for Canvas API Student operations
 * 
 * @method Lookup<Models\Group, Models\Student> getStudentsInGroups() Virtual method to get all student in groups
 */
class StudentProvider extends AbstractProvider{
    public function __construct(
        public readonly Services\StatusHandlerInterface $statusHandler
    ){}

    /**
     * Summary of getStudentsInGroup
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @param \CanvasApiLibrary\Models\Group $group
     * @return Student[]
     */
    public function getStudentsInGroup(Domain $domain, Models\Group $group): array{
        return $this->Get($domain, "/groups/{$group->id}/users");
    }

    public function MapData(mixed $data, Domain $domain, array $suplementaryDataMapping = []): array{
        return array_map_to_models($data, $domain, Student::class, ["name", ...$suplementaryDataMapping]);
    }
}