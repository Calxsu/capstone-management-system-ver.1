<?php

namespace App\Actions;

use App\Models\PanelMember;
use App\Repositories\PanelMemberRepositoryInterface;

class ComputeETLAction
{
    private const ETL_VALUES = [
        'Adviser' => 0.5,
        'Chair' => 0.3,
        'Critique' => 0.3,
    ];

    public function __construct(
        private PanelMemberRepositoryInterface $panelMemberRepository
    ) {}

    public function execute(int $panelMemberId): float
    {
        $panelMember = $this->panelMemberRepository->find($panelMemberId);

        if (!$panelMember) {
            throw new \Exception("Panel member not found");
        }

        $totalETL = $panelMember->etl_base ?? 0;

        // Calculate ETL from group assignments
        foreach ($panelMember->groups as $group) {
            $role = $group->pivot->role;
            if (isset(self::ETL_VALUES[$role])) {
                $totalETL += self::ETL_VALUES[$role];
            }
        }

        return $totalETL;
    }

    public function executeForAll(): array
    {
        $panelMembers = $this->panelMemberRepository->all();
        $results = [];

        foreach ($panelMembers as $panelMember) {
            $results[$panelMember->id] = [
                'email' => $panelMember->email,
                'etl_total' => $this->execute($panelMember->id),
            ];
        }

        return $results;
    }
}