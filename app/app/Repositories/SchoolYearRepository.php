<?php

namespace App\Repositories;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Collection;

class SchoolYearRepository implements SchoolYearRepositoryInterface
{
    public function all(): Collection
    {
        return SchoolYear::all();
    }

    public function find(int $id): ?SchoolYear
    {
        return SchoolYear::find($id);
    }

    public function create(array $data): SchoolYear
    {
        return SchoolYear::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $schoolYear = SchoolYear::find($id);
        return $schoolYear ? $schoolYear->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $schoolYear = SchoolYear::find($id);
        return $schoolYear ? $schoolYear->delete() : false;
    }
}