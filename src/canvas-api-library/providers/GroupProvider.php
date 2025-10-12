<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Services as Services;
class GroupProvider{

    /**
     * Summary of getGroups
     * @return Models\Group[]
     */
    protected function getAllGroupsInGroupCategory(Models\GroupCategory $category, Services\CanvasGlobalCommunicator $communicator) : array{
        /*
        $url = "$this->baseURL/group_categories/$groupSetID/groups";
        $data = curlCall($url, $this->apiKey);
        return $data;
         */
        $data = $communicator->get("/group_categories/{$category->id}/groups");
        if(isset($data["status"]) && $data["status"] == "not found"){//TODO put in the curl call itself?
            throw new \Exception("Groupset with id $groupSetID not found. Did you remove this group set?");
        }
        return array_map(fn($x) => new Models\Group($x["id"], $x["name"]), $data);
    }

    /**
     * Get all groups, including students in each group
     * @return Models\Group[]
     */
    public function getAllGroupsWithStudents(): array{
        $groups = $this->getAllGroups();
        foreach($groups as $group){
            $group->students = $this->getStudentsInGroup($group->id);
        }
        return $groups;
    }

    public function getStudentGroupLookup(): Util\Lookup{
        $groups = $this->getAllGroupsWithStudents();
        $lookup = new Util\Lookup();
        foreach($groups as $group){
            foreach($group->students as $student){
                $lookup->add($student, $group);
            }
        }
        return $lookup;
    }
}

class GroupProvider extends UncachedGroupProvider{
    public function getStudentsInGroup(int $groupID): array{
        global $veryLongTimeout;
        return cached_call(new \MaximumAPIKeyRestrictions(), $veryLongTimeout,
        fn() => parent::getStudentsInGroup($groupID),
        "GroupProvider - getStudentsInGroup", $groupID);
    }
    protected function getAllGroups(): array{
        global $veryLongTimeout;
        return cached_call(new \MaximumAPIKeyRestrictions(), $veryLongTimeout,
        fn() => parent::getAllGroups(),
        "GroupProvider - getAllGroups");
    }
    public function getAllGroupsWithStudents(): array{
        global $veryLongTimeout;
        return cached_call(new \MaximumAPIKeyRestrictions(), $veryLongTimeout,
        fn() => parent::getAllGroupsWithStudents(),
        "GroupProvider - getAllGroupsWithStudents");
    }

    // public function
}