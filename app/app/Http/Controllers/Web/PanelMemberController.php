<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PanelMember;
use Illuminate\Http\Request;

class PanelMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('panel-members.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel-members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:panel_members,email',
            'specialization' => 'nullable|in:Networking,Systems Development',
            'status' => 'required|in:active,inactive',
        ]);

        PanelMember::create([
            'email' => $request->email,
            'specialization' => $request->specialization,
            'status' => $request->status,
        ]);

        return redirect()->route('panel-members.index')->with('success', 'Panel member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PanelMember $panelMember)
    {
        $panelMember->load(['groups.schoolYear']);
        $groupsByRole = $panelMember->getGroupCountByRole();
        $totalEtl = $panelMember->calculateTotalEtl();
        
        return view('panel-members.show', compact('panelMember', 'groupsByRole', 'totalEtl'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PanelMember $panelMember)
    {
        return view('panel-members.edit', compact('panelMember'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PanelMember $panelMember)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:panel_members,email,' . $panelMember->id,
            'specialization' => 'nullable|in:Networking,Systems Development',
            'status' => 'required|in:active,inactive',
        ]);

        $panelMember->update([
            'email' => $request->email,
            'specialization' => $request->specialization,
            'status' => $request->status,
        ]);

        return redirect()->route('panel-members.index')->with('success', 'Panel member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PanelMember $panelMember)
    {
        $panelMember->delete();

        return redirect()->route('panel-members.index')->with('success', 'Panel member deleted successfully.');
    }

    /**
     * Get panel members data for AJAX
     */
    public function getData()
    {
        $members = PanelMember::all()->map(function ($member) {
            $groupsByRole = $member->getGroupCountByRole();
            return [
                'id' => $member->id,
                'email' => $member->email,
                'specialization' => $member->specialization,
                'status' => $member->status,
                'roles' => array_keys($groupsByRole),
                'groups_count' => array_sum($groupsByRole),
                'adviser_count' => $groupsByRole['Adviser'] ?? 0,
                'chair_count' => $groupsByRole['Chair'] ?? 0,
                'critique_count' => $groupsByRole['Critique'] ?? 0,
            ];
        });

        return response()->json([
            'data' => $members,
            'stats' => [
                'total' => $members->count(),
                'active' => $members->where('status', 'active')->count(),
                'inactive' => $members->where('status', 'inactive')->count(),
            ]
        ]);
    }
}
