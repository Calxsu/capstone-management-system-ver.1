<?php

namespace App\Repositories;

use App\Models\PanelMember;
use Illuminate\Database\Eloquent\Collection;

class PanelMemberRepository implements PanelMemberRepositoryInterface
{
    public function all(): Collection
    {
        return PanelMember::with('groups')->get();
    }

    public function find(int $id): ?PanelMember
    {
        return PanelMember::with('groups')->find($id);
    }

    public function create(array $data): PanelMember
    {
        return PanelMember::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $panelMember = PanelMember::find($id);
        return $panelMember ? $panelMember->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $panelMember = PanelMember::find($id);
        return $panelMember ? $panelMember->delete() : false;
    }

    public function findByRole(string $role): Collection
    {
        return PanelMember::where('role', $role)->get();
    }
}