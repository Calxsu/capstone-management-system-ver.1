<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('evaluations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('evaluations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'panel_member_id' => 'required|exists:panel_members,id',
            'cap_stage' => 'required|integer|min:1|max:2',
            'grade' => 'required|numeric|min:0|max:100',
            'evaluation_date' => 'required|date',
            'remarks' => 'nullable|string|max:2000',
        ]);

        // Check if evaluation already exists for this combination
        $existing = Evaluation::where([
            'group_id' => $validated['group_id'],
            'panel_member_id' => $validated['panel_member_id'],
            'cap_stage' => $validated['cap_stage'],
        ])->exists();

        if ($existing) {
            return back()->withErrors(['panel_member_id' => 'Evaluation already exists for this panel member and CAPSTONE stage'])->withInput();
        }

        Evaluation::create([
            'group_id' => $validated['group_id'],
            'panel_member_id' => $validated['panel_member_id'],
            'cap_stage' => $validated['cap_stage'],
            'grade' => $validated['grade'],
            'date' => $validated['evaluation_date'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('evaluations.index')->with('success', 'Evaluation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $evaluation = Evaluation::with(['group.panelMembers', 'group.students', 'panelMember'])->findOrFail($id);
        return view('evaluations.show', compact('evaluation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $evaluation = Evaluation::with(['group.panelMembers', 'panelMember'])->findOrFail($id);
        return view('evaluations.edit', compact('evaluation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $evaluation = Evaluation::findOrFail($id);
        
        $validated = $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'evaluation_date' => 'required|date',
            'remarks' => 'nullable|string|max:2000',
        ]);

        $evaluation->update([
            'grade' => $validated['grade'],
            'date' => $validated['evaluation_date'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('evaluations.index')->with('success', 'Evaluation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->delete();
        
        return redirect()->route('evaluations.index')->with('success', 'Evaluation deleted successfully.');
    }

    /**
     * Display evaluations for a specific group.
     */
    public function groupEvaluations(Group $group): View
    {
        return view('evaluations.group', compact('group'));
    }

    /**
     * Show the form for creating an evaluation for a specific group.
     */
    public function createForGroup(Group $group): View
    {
        return view('evaluations.create', compact('group'));
    }

    /**
     * Show the form for editing all evaluations for a group's CAP stage.
     */
    public function editGroup(Request $request, Group $group): View
    {
        $capStage = $request->query('cap_stage', 1);
        
        // Load group with panel members and students
        $group->load(['panelMembers', 'students', 'schoolYear']);
        
        // Get existing evaluations for this group and cap stage
        $evaluations = Evaluation::where('group_id', $group->id)
            ->where('cap_stage', $capStage)
            ->get()
            ->keyBy('panel_member_id');
        
        return view('evaluations.edit-group', compact('group', 'capStage', 'evaluations'));
    }

    /**
     * Update all evaluations for a group's CAP stage.
     */
    public function updateGroup(Request $request, Group $group)
    {
        $validated = $request->validate([
            'cap_stage' => 'required|integer|min:1|max:2',
            'date' => 'required|date',
            'grades' => 'required|array',
            'grades.*' => 'nullable|numeric|min:0|max:100',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:2000',
        ]);

        $capStage = $validated['cap_stage'];
        $date = $validated['date'];
        $grades = $validated['grades'];
        $remarks = $validated['remarks'] ?? [];

        $updatedEvaluations = [];

        foreach ($grades as $panelMemberId => $grade) {
            if ($grade === null || $grade === '') {
                // Skip empty grades - optionally delete existing evaluation
                continue;
            }

            // Find existing or create new evaluation
            $evaluation = Evaluation::updateOrCreate(
                [
                    'group_id' => $group->id,
                    'panel_member_id' => $panelMemberId,
                    'cap_stage' => $capStage,
                ],
                [
                    'grade' => $grade,
                    'date' => $date,
                    'remarks' => $remarks[$panelMemberId] ?? null,
                    'student_id' => $group->students->first()?->id, // Reference first student
                ]
            );

            $updatedEvaluations[] = $evaluation;
        }

        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Grades updated successfully.',
                'evaluations' => $updatedEvaluations
            ]);
        }

        return redirect()->route('evaluations.index')->with('success', 'Grades updated successfully.');
    }
}
