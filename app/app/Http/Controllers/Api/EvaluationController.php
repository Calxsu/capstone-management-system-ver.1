<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /**
     * Display a listing of all evaluable groups with their evaluations.
     * Returns ALL groups that have panel members assigned (ready for evaluation),
     * regardless of whether they have evaluations yet.
     */
    public function index(Request $request): JsonResponse
    {
        // Get all groups that have panel members assigned (evaluable groups)
        $groups = Group::with(['panelMembers', 'students', 'schoolYear'])
            ->whereHas('panelMembers') // Only groups with assigned panel members
            ->get();

        // Get all evaluations
        $evaluations = Evaluation::with(['panelMember', 'student'])->get();

        // Build the response: all evaluable groups with their evaluations
        $result = $groups->map(function($group) use ($evaluations) {
            $groupEvals = $evaluations->where('group_id', $group->id);
            
            return [
                'group_id' => $group->id,
                'group' => [
                    'id' => $group->id,
                    'project_title' => $group->project_title,
                    'cap_stage' => $group->cap_stage,
                    'cap_status' => $group->cap_status,
                    'defense_status' => $group->defense_status,
                    'students_count' => $group->students->count(),
                    'students' => $group->students,
                    'panel_members' => $group->panelMembers,
                    'school_year' => $group->schoolYear,
                ],
                'evaluations' => $groupEvals->values(),
            ];
        });

        // Apply filters if provided
        if ($request->has('group_id')) {
            $result = $result->where('group_id', $request->group_id)->values();
        }

        return response()->json($result);
    }

    /**
     * Store a new evaluation (grade input).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'panel_member_id' => 'required|exists:panel_members,id',
            'student_id' => 'nullable|exists:students,id',
            'cap_stage' => 'required|integer|min:1|max:2',
            'grade' => 'required|numeric|min:0|max:100',
            'criteria' => 'nullable|string|max:1000',
            'remarks' => 'nullable|string|max:2000',
            'date' => 'required|date',
        ]);

        // Check if evaluation already exists for this combination and CAP stage
        $existing = Evaluation::where([
            'group_id' => $validated['group_id'],
            'panel_member_id' => $validated['panel_member_id'],
            'cap_stage' => $validated['cap_stage'],
        ]);
        
        if (isset($validated['student_id'])) {
            $existing->where('student_id', $validated['student_id']);
        }
        
        if ($existing->exists()) {
            return response()->json([
                'message' => 'Evaluation already exists for this panel member and CAP stage'
            ], 422);
        }

        // Verify that the panel member is assigned to the group
        $panelAssigned = DB::table('group_panels')
            ->where('group_id', $validated['group_id'])
            ->where('panel_member_id', $validated['panel_member_id'])
            ->exists();

        if (!$panelAssigned) {
            return response()->json([
                'message' => 'Panel member is not assigned to this group'
            ], 422);
        }

        // Verify that the student is in the group (if student_id provided)
        if (isset($validated['student_id'])) {
            $studentInGroup = DB::table('group_students')
                ->where('group_id', $validated['group_id'])
                ->where('student_id', $validated['student_id'])
                ->exists();

            if (!$studentInGroup) {
                return response()->json([
                    'message' => 'Student is not in this group'
                ], 422);
            }
        }

        $evaluation = Evaluation::create($validated);
        return response()->json($evaluation->load(['group', 'panelMember', 'student']), 201);
    }

    /**
     * Display the specified evaluation.
     */
    public function show(int $id): JsonResponse
    {
        $evaluation = Evaluation::with(['group', 'panelMember', 'student'])->find($id);

        if (!$evaluation) {
            return response()->json(['message' => 'Evaluation not found'], 404);
        }

        return response()->json($evaluation);
    }

    /**
     * Update the specified evaluation.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $evaluation = Evaluation::find($id);

        if (!$evaluation) {
            return response()->json(['message' => 'Evaluation not found'], 404);
        }

        $validated = $request->validate([
            'grade' => 'sometimes|numeric|min:0|max:100',
            'criteria' => 'sometimes|nullable|string|max:1000',
            'date' => 'sometimes|date',
        ]);

        $evaluation->update($validated);
        return response()->json($evaluation->load(['group', 'panelMember', 'student']));
    }

    /**
     * Remove the specified evaluation.
     */
    public function destroy(int $id): JsonResponse
    {
        $evaluation = Evaluation::find($id);

        if (!$evaluation) {
            return response()->json(['message' => 'Evaluation not found'], 404);
        }

        $evaluation->delete();
        return response()->json(['message' => 'Evaluation deleted successfully']);
    }

    /**
     * Get evaluation summary for a group.
     */
    public function groupSummary(int $groupId): JsonResponse
    {
        $evaluations = Evaluation::where('group_id', $groupId)
            ->with(['student', 'panelMember'])
            ->get();

        $summary = [
            'group_id' => $groupId,
            'total_evaluations' => $evaluations->count(),
            'students_evaluated' => $evaluations->pluck('student_id')->unique()->count(),
            'average_grades' => [],
            'evaluations_by_student' => [],
        ];

        // Group evaluations by student
        $byStudent = $evaluations->groupBy('student_id');
        foreach ($byStudent as $studentId => $studentEvaluations) {
            $student = $studentEvaluations->first()->student;
            $grades = $studentEvaluations->pluck('grade');

            $summary['evaluations_by_student'][$studentId] = [
                'student_name' => $student->name,
                'student_id' => $student->student_id,
                'evaluations_count' => $studentEvaluations->count(),
                'grades' => $grades,
                'average_grade' => $grades->avg(),
                'min_grade' => $grades->min(),
                'max_grade' => $grades->max(),
                'evaluations' => $studentEvaluations->map(function ($eval) {
                    return [
                        'panel_member' => $eval->panelMember->email,
                        'role' => $eval->panelMember->role,
                        'grade' => $eval->grade,
                        'criteria' => $eval->criteria,
                        'date' => $eval->date,
                    ];
                }),
            ];
        }

        return response()->json($summary);
    }
}
