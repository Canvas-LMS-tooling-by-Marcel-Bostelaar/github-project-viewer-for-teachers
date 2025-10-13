<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Services\CanvasGlobalCommunicator;

/**
 * Provider for Canvas API group operations
 * 
 * @method \CanvasApiLibrary\Providers\Lookup<Models\GroupCategory, Models\Group> GetAllGroupsInGroupCategories() Virtual method to get all groups in group categories
 * @method int GetAllGroupsInGroupCategories2() Virtual method to get all groups in group categories
 */
class GroupProvider extends AbstractProvider{
    public function __construct(public readonly Services\StatusHandlerInterface $statusHandler){}

    /**
     * Summary of getAllGroupsInGroupCategory
     * @param \CanvasApiLibrary\Models\GroupCategory $category
     * @param \CanvasApiLibrary\Services\CanvasGlobalCommunicator $communicator
     * @return Models\Group[]
     */
    public function getAllGroupsInGroupCategory(Models\GroupCategory $category, CanvasGlobalCommunicator $communicator) : array{
        return $this->Get($communicator, 
        "/group_categories/{$category->id}/groups");
    }

    public function MapData(mixed $data, array $suplementaryDataMapping = []): array{
        return array_map_to_models($data, Group::class, ["name", ...$suplementaryDataMapping]);
    }
}

$test = new GroupProvider(null);
$test->GetAllGroupsInGroupCategories();