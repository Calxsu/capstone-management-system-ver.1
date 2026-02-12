<?php

namespace App\Repositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

interface GroupRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Group;
    public function create(array $data): Group;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findBySchoolYear(int $schoolYearId): Collection;
    public function findByCapStatus(string $capStatus): Collection;
}