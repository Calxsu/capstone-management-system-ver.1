<?php

namespace App\Repositories;

use App\Models\PanelMember;
use Illuminate\Database\Eloquent\Collection;

interface PanelMemberRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?PanelMember;
    public function create(array $data): PanelMember;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByRole(string $role): Collection;
}