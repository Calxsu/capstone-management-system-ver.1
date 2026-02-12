<?php

namespace App\Actions;

use App\Models\ChangesLog;
use App\Models\Group;
use App\Repositories\GroupRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateGroupStatusAction
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository
    ) {}

    public function execute(int $groupId, string $newStatus, string $reason): array
    {
        $group = $this->groupRepository->find($groupId);

        if (!$group) {
            throw new \Exception("Group not found");
        }

        // Validate status transition
        $this->validateStatusTransition($group->cap_status, $newStatus);

        // Additional validation for CAP2 transition
        if ($newStatus === 'CAP2') {
            $this->validateCap2Transition($group);
        }

        DB::transaction(function () use ($group, $newStatus, $reason) {
            $oldStatus = $group->cap_status;

            // Update group status
            $group->update(['cap_status' => $newStatus]);

            // Log the change
            $changeLog = ChangesLog::create([
                'group_id' => $group->id,
                'change_type' => 'status_change',
                'old_value' => json_encode([
                    'cap_status' => $oldStatus,
                ]),
                'new_value' => json_encode([
                    'cap_status' => $newStatus,
                    'reason' => $reason,
                ]),
                'changed_by' => 'system',
                'timestamp' => now(),
            ]);

            return $changeLog;
        });

        // Reload group with relationships
        $updatedGroup = $this->groupRepository->find($groupId);

        return [
            'group' => $updatedGroup,
            'change_log' => $changeLog ?? null,
        ];
    }

    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $validTransitions = [
            'CAP1' => ['CAP2'],
            'CAP2' => [], // CAPSTONE 2 is final status (includes publication)
        ];

        if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            throw new \Exception("Invalid status transition from {$currentStatus} to {$newStatus}");
        }
    }

    private function validateCap2Transition(Group $group): void
    {
        // Check if all students have been evaluated
        $totalStudents = $group->students()->count();
        $evaluatedStudents = $group->evaluations()
            ->select('student_id')
            ->groupBy('student_id')
            ->get()
            ->count();

        if ($totalStudents !== $evaluatedStudents) {
            throw new \Exception("All students must be evaluated before transitioning to CAPSTONE 2");
        }

        // Check if group has minimum required members (at least 1 student)
        if ($totalStudents < 1) {
            throw new \Exception("Group must have at least 1 student before transitioning to CAPSTONE 2");
        }

        // Check if all panel members are assigned
        $panelCount = $group->panelMembers()->count();
        if ($panelCount < 3) {
            throw new \Exception("Group must have all 3 panel members assigned before transitioning to CAPSTONE 2");
        }
    }
}