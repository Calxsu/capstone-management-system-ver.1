<?php

namespace App\Repositories;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Collection;

interface SchoolYearRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?SchoolYear;
    public function create(array $data): SchoolYear;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}