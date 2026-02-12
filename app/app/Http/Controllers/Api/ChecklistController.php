<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\Group;
use App\Models\GroupChecklist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends Controller
{
    /**
     * Get all active checklist items (template items).
     */
    public function items(): JsonResponse
    {
        $items = ChecklistItem::active()->ordered()->get();
        return response()->json($items);
    }

    /**
     * Store a new checklist item.
     */
    public function storeItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer',
            'is_required' => 'boolean',
        ]);

        $item = ChecklistItem::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? ChecklistItem::max('order') + 1,
            'is_required' => $validated['is_required'] ?? true,
        ]);

        return response()->json($item, 201);
    }

    /**
     * Update a checklist item.
     */
    public function updateItem(Request $request, ChecklistItem $checklistItem): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'order' => 'nullable|integer',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $checklistItem->update($validated);

        return response()->json($checklistItem);
    }

    /**
     * Delete a checklist item.
     */
    public function destroyItem(ChecklistItem $checklistItem): JsonResponse
    {
        $checklistItem->update(['is_active' => false]);
        return response()->json(['message' => 'Checklist item deactivated']);
    }

    /**
     * Get checklist status for a specific group.
     */
    public function groupChecklist(Group $group): JsonResponse
    {
        // Get all active checklist items
        $items = ChecklistItem::active()->ordered()->get();
        
        // Get the group's checklist completions
        $completions = $group->checklists()->with('completedByUser')->get()->keyBy('checklist_item_id');

        // Build the response
        $checklist = $items->map(function ($item) use ($completions) {
            $completion = $completions->get($item->id);
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'is_required' => $item->is_required,
                'is_completed' => $completion?->is_completed ?? false,
                'completed_at' => $completion?->completed_at,
                'completed_by' => $completion?->completedByUser?->name,
                'notes' => $completion?->notes,
            ];
        });

        // Calculate progress
        $totalRequired = $items->where('is_required', true)->count();
        $completedRequired = $items->filter(function ($item) use ($completions) {
            return $item->is_required && ($completions->get($item->id)?->is_completed ?? false);
        })->count();

        return response()->json([
            'group_id' => $group->id,
            'items' => $checklist,
            'progress' => [
                'total' => $items->count(),
                'completed' => $completions->where('is_completed', true)->count(),
                'required_total' => $totalRequired,
                'required_completed' => $completedRequired,
                'percentage' => $totalRequired > 0 ? round(($completedRequired / $totalRequired) * 100) : 100,
            ],
        ]);
    }

    /**
     * Toggle a checklist item for a group.
     */
    public function toggleItem(Request $request, Group $group, ChecklistItem $checklistItem): JsonResponse
    {
        $validated = $request->validate([
            'is_completed' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $checklist = GroupChecklist::updateOrCreate(
            [
                'group_id' => $group->id,
                'checklist_item_id' => $checklistItem->id,
            ],
            [
                'is_completed' => $validated['is_completed'],
                'completed_at' => $validated['is_completed'] ? now() : null,
                'completed_by' => $validated['is_completed'] ? Auth::id() : null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'message' => $validated['is_completed'] ? 'Item marked as complete' : 'Item marked as incomplete',
            'checklist' => $checklist->load('completedByUser'),
        ]);
    }

    /**
     * Get all CAPSTONE 2 groups with their checklist progress.
     */
    public function cap2GroupsProgress(): JsonResponse
    {
        $groups = Group::where('cap_stage', 2)
            ->with(['students', 'checklists'])
            ->withCount([
                'checklists as completed_count' => function ($query) {
                    $query->where('is_completed', true);
                }
            ])
            ->get();

        $totalItems = ChecklistItem::active()->where('is_required', true)->count();

        $result = $groups->map(function ($group) use ($totalItems) {
            return [
                'id' => $group->id,
                'project_title' => $group->project_title,
                'students_count' => $group->students->count(),
                'completed_items' => $group->completed_count,
                'total_items' => $totalItems,
                'progress_percentage' => $totalItems > 0 ? round(($group->completed_count / $totalItems) * 100) : 100,
                'is_complete' => $group->completed_count >= $totalItems,
            ];
        });

        return response()->json($result);
    }
}
