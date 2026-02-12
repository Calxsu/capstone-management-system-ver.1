<?php

namespace App\Http\Controllers\Api;

use App\Actions\UpdateGroupStatusAction;
use App\Http\Controllers\Controller;
use App\Models\ChangesLog;
use App\Models\Group;
use App\Models\Student;
use App\Repositories\GroupRepositoryInterface;
use App\Repositories\StudentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function __construct(
        private GroupRepositoryInterface $groupRepository,
        private StudentRepositoryInterface $studentRepository,
        private UpdateGroupStatusAction $updateGroupStatusAction
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Group::with(['schoolYear', 'students', 'panelMembers']);

        if ($request->has('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        }

        if ($request->has('cap_status')) {
            $query->where('cap_status', $request->cap_status);
        }

        $groups = $query->get();
        return response()->json($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'project_title' => 'nullable|string|max:255',
            'cap_stage' => 'integer|min:1|max:2',
            'cap_status' => 'in:CAP1,CAP2',
            'title_optional' => 'nullable|string|max:255',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
            'panel_ids' => 'nullable|array',
            'panel_ids.*' => 'exists:panel_members,id',
            'panel_roles' => 'nullable|array',
        ]);

        // Convert cap_status to cap_stage if provided
        if (isset($validated['cap_status']) && !isset($validated['cap_stage'])) {
            $validated['cap_stage'] = (int) filter_var($validated['cap_status'], FILTER_SANITIZE_NUMBER_INT);
        }

        // Remove relationship arrays from validated data for group creation
        $studentIds = $validated['student_ids'] ?? [];
        $panelIds = $validated['panel_ids'] ?? [];
        $panelRoles = $validated['panel_roles'] ?? [];
        unset($validated['student_ids'], $validated['panel_ids'], $validated['panel_roles']);

        $group = $this->groupRepository->create($validated);

        // Attach students
        if (!empty($studentIds)) {
            $group->students()->attach($studentIds);
        }

        // Attach panel members with roles
        if (!empty($panelIds)) {
            $panelData = [];
            foreach ($panelIds as $index => $panelId) {
                $role = $panelRoles[$index] ?? 'Critique';
                $panelData[$panelId] = ['role' => $role];
            }
            $group->panelMembers()->attach($panelData);
        }

        return response()->json($group->load(['schoolYear', 'students', 'panelMembers']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $group = $this->groupRepository->find($id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        return response()->json($group->load(['schoolYear', 'students', 'panelMembers', 'evaluations']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'project_title' => 'sometimes|nullable|string|max:255',
            'cap_stage' => 'sometimes|integer|min:1|max:2',
            'cap_status' => 'sometimes|in:CAP1,CAP2',
            'title_optional' => 'sometimes|nullable|string|max:255',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
            'panel_ids' => 'nullable|array',
            'panel_ids.*' => 'exists:panel_members,id',
            'panel_roles' => 'nullable|array',
        ]);

        $group = $this->groupRepository->find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Extract relationship arrays
        $studentIds = $request->has('student_ids') ? ($validated['student_ids'] ?? []) : null;
        $panelIds = $request->has('panel_ids') ? ($validated['panel_ids'] ?? []) : null;
        $panelRoles = $validated['panel_roles'] ?? [];
        unset($validated['student_ids'], $validated['panel_ids'], $validated['panel_roles']);

        // Update group basic info
        $this->groupRepository->update($id, $validated);

        // Sync students if provided
        if ($studentIds !== null) {
            $group->students()->sync($studentIds);
        }

        // Sync panel members with roles if provided
        if ($panelIds !== null) {
            $panelData = [];
            foreach ($panelIds as $index => $panelId) {
                $role = $panelRoles[$index] ?? 'Critique';
                $panelData[$panelId] = ['role' => $role];
            }
            $group->panelMembers()->sync($panelData);
        }

        return response()->json($group->fresh()->load(['schoolYear', 'students', 'panelMembers']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->groupRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        return response()->json(['message' => 'Group deleted successfully']);
    }

    /**
     * Add a student to a group.
     */
    public function addStudent(Request $request, int $groupId): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'reason' => 'required|string|max:500',
        ]);

        $group = $this->groupRepository->find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $student = $this->studentRepository->find($request->student_id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Check if student is already in the group
        if ($group->students()->where('student_id', $student->id)->exists()) {
            return response()->json(['message' => 'Student is already in this group'], 422);
        }

        // Check group size limit (max 3 students)
        if ($group->students()->count() >= 3) {
            return response()->json(['message' => 'Group already has maximum number of students (3)'], 422);
        }

        DB::transaction(function () use ($group, $student, $request) {
            // Add student to group
            $group->students()->attach($student->id);

            // Log the change
            ChangesLog::create([
                'group_id' => $group->id,
                'change_type' => 'student_added',
                'old_value' => null,
                'new_value' => json_encode([
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'reason' => $request->reason,
                ]),
                'changed_by' => 'system',
                'timestamp' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Student added to group successfully',
            'group' => $group->load('students')
        ]);
    }

    /**
     * Remove a student from a group.
     */
    public function removeStudent(Request $request, int $groupId, int $studentId): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $group = $this->groupRepository->find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $student = $this->studentRepository->find($studentId);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Check if student is in the group
        if (!$group->students()->where('student_id', $student->id)->exists()) {
            return response()->json(['message' => 'Student is not in this group'], 422);
        }

        DB::transaction(function () use ($group, $student, $request) {
            // Remove student from group
            $group->students()->detach($student->id);

            // Log the change
            ChangesLog::create([
                'group_id' => $group->id,
                'change_type' => 'student_removed',
                'old_value' => json_encode([
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                ]),
                'new_value' => json_encode([
                    'reason' => $request->reason,
                ]),
                'changed_by' => 'system',
                'timestamp' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Student removed from group successfully',
            'group' => $group->load('students')
        ]);
    }

    /**
     * Update group status (CAP stage transition).
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'cap_stage' => 'required|integer|min:1|max:2',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $capStatus = 'CAP' . $request->cap_stage;
            $result = $this->updateGroupStatusAction->execute($id, $capStatus, $request->reason);

            return response()->json([
                'message' => 'Group status updated successfully',
                'group' => $result['group'],
                'change_log' => $result['change_log'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update group status',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
