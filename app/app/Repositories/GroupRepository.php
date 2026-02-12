<?php

namespace App\Repositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository implements GroupRepositoryInterface
{
    public function all(): Collection
    {
        return Group::with(['schoolYear', 'students', 'panelMembers'])->get();
    }

    public function find(int $id): ?Group
    {
        return Group::with(['schoolYear', 'students', 'panelMembers'])->find($id);
    }

    public function create(array $data): Group
    {
        return Group::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $group = Group::find($id);
        return $group ? $group->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $group = Group::find($id);
        return $group ? $group->delete() : false;
    }

    public function findBySchoolYear(int $schoolYearId): Collection
    {
        return Group::where('school_year_id', $schoolYearId)->get();
    }

    public function findByCapStatus(string $capStatus): Collection
    {
        return Group::where('cap_status', $capStatus)->get();
    }
}