<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PanelMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PanelMemberController extends Controller
{
    /**
     * Display a listing of panel members.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PanelMember::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('email', 'like', "%{$search}%");
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'email');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination or all
            if ($request->has('per_page')) {
                $panelMembers = $query->paginate($request->get('per_page', 15));
            } else {
                $panelMembers = $query->get();
            }

            return response()->json($panelMembers);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch panel members',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created panel member.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255|unique:panel_members,email',
                'specialization' => 'nullable|in:Networking,Systems Development',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Set default status if not provided
            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }

            $panelMember = PanelMember::create($validated);

            return response()->json([
                'message' => 'Panel member created successfully',
                'data' => $panelMember
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create panel member',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified panel member.
     */
    public function show(PanelMember $panelMember): JsonResponse
    {
        try {
            $panelMember->load('groups');
            return response()->json($panelMember);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch panel member',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified panel member.
     */
    public function update(Request $request, PanelMember $panelMember): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'sometimes|required|email|max:255|unique:panel_members,email,' . $panelMember->id,
                'specialization' => 'nullable|in:Networking,Systems Development',
                'status' => 'nullable|in:active,inactive',
            ]);

            $panelMember->update($validated);

            return response()->json([
                'message' => 'Panel member updated successfully',
                'data' => $panelMember
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update panel member',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified panel member.
     */
    public function destroy(PanelMember $panelMember): JsonResponse
    {
        try {
            // Check if panel member is assigned to any groups
            if ($panelMember->groups()->count() > 0) {
                return response()->json([
                    'error' => 'Cannot delete panel member',
                    'message' => 'This panel member is assigned to one or more groups'
                ], 409);
            }

            $panelMember->delete();

            return response()->json([
                'message' => 'Panel member deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete panel member',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available panel members (not assigned to any group)
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $query = PanelMember::whereDoesntHave('groups');
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('email', 'like', "%{$search}%");
            }
            
            $available = $query->get();
            return response()->json($available);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch available panel members',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
