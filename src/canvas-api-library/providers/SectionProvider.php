<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Section;
use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Services\CanvasGlobalCommunicator;
class SectionProvider extends AbstractProvider{
    public function __construct(public readonly Services\StatusHandlerInterface $statusHandler){}

    /**
     * Summary of getAllGroupsInGroupCategory
     * @param \CanvasApiLibrary\Models\GroupCategory $category
     * @param \CanvasApiLibrary\Services\CanvasGlobalCommunicator $communicator
     * @return Models\Section[]
     */
    public function getAllSectionsInCourse(CanvasGlobalCommunicator $communicator) : array{
        return $this->Get($communicator, 
        "/sections");
    }

    public function MapData(mixed $data, array $suplementaryDataMapping = []): array{
        return array_map_to_models($data, Section::class, [
            "name", 
            ["course_id", fn($v) => new Models\Course($v)],
            ...$suplementaryDataMapping]);
    }
}
