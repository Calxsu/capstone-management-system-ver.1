<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PanelMember;
use App\Models\SchoolYear;
use App\Models\EtlRoleValue;
use Illuminate\Http\Request;

class EtlController extends Controller
{
    /**
     * Display the ETL management page
     */
    public function index(Request $request)
    {
        $schoolYearId = $request->get('school_year_id');
        $schoolYears = SchoolYear::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();
        
        return view('etl.index', compact('schoolYears', 'schoolYearId'));
    }

    /**
     * Get ETL data for all panel members
     */
    public function getData(Request $request)
    {
        $schoolYearId = $request->get('school_year_id');
        $panelMembers = PanelMember::where('status', 'active')->get();
        $roleValues = EtlRoleValue::getAllValues();

        $etlData = [];

        foreach ($panelMembers as $member) {
            if ($schoolYearId) {
                // Calculate ETL for specific semester
                $etlInfo = $member->calculateEtlForSemester($schoolYearId);
                $etlData[] = [
                    'id' => $member->id,
                    'name' => $member->name,
                    'specialization' => $member->specialization,
                    'adviser_count' => $etlInfo['breakdown']['Adviser']['count'],
                    'adviser_etl' => $etlInfo['breakdown']['Adviser']['total'],
                    'chair_count' => $etlInfo['breakdown']['Chair']['count'],
                    'chair_etl' => $etlInfo['breakdown']['Chair']['total'],
                    'critique_count' => $etlInfo['breakdown']['Critique']['count'],
                    'critique_etl' => $etlInfo['breakdown']['Critique']['total'],
                    'carry_over' => $etlInfo['carry_over'],
                    'total_etl' => $etlInfo['total'],
                ];
            } else {
                // Calculate total ETL across all semesters
                $totalEtl = $member->calculateTotalEtl();
                $combinedBreakdown = [
                    'Adviser' => ['count' => 0, 'total' => 0],
                    'Chair' => ['count' => 0, 'total' => 0],
                    'Critique' => ['count' => 0, 'total' => 0],
                ];
                $totalCarryOver = 0;

                foreach ($totalEtl['semesters'] as $semester) {
                    foreach ($semester['etl']['breakdown'] as $role => $data) {
                        $combinedBreakdown[$role]['count'] += $data['count'];
                        $combinedBreakdown[$role]['total'] += $data['total'];
                    }
                    $totalCarryOver += $semester['etl']['carry_over'];
                }

                $etlData[] = [
                    'id' => $member->id,
                    'name' => $member->name,
                    'specialization' => $member->specialization,
                    'adviser_count' => $combinedBreakdown['Adviser']['count'],
                    'adviser_etl' => $combinedBreakdown['Adviser']['total'],
                    'chair_count' => $combinedBreakdown['Chair']['count'],
                    'chair_etl' => $combinedBreakdown['Chair']['total'],
                    'critique_count' => $combinedBreakdown['Critique']['count'],
                    'critique_etl' => $combinedBreakdown['Critique']['total'],
                    'carry_over' => $totalCarryOver,
                    'total_etl' => $totalEtl['grand_total'],
                ];
            }
        }

        // Sort by total ETL descending
        usort($etlData, fn($a, $b) => $b['total_etl'] <=> $a['total_etl']);

        return response()->json([
            'data' => $etlData,
            'role_values' => $roleValues,
            'stats' => [
                'total_members' => count($etlData),
                'total_etl' => array_sum(array_column($etlData, 'total_etl')),
                'avg_etl' => count($etlData) > 0 ? array_sum(array_column($etlData, 'total_etl')) / count($etlData) : 0,
            ]
        ]);
    }

    /**
     * Show ETL details for a specific panel member
     */
    public function show(PanelMember $panelMember)
    {
        $totalEtl = $panelMember->calculateTotalEtl();
        $groupsByRole = $panelMember->getGroupCountByRole();
        
        return view('etl.show', compact('panelMember', 'totalEtl', 'groupsByRole'));
    }

    /**
     * Get ETL details for a specific panel member (API)
     */
    public function getMemberDetails(PanelMember $panelMember)
    {
        $totalEtl = $panelMember->calculateTotalEtl();
        $groupsByRole = $panelMember->getGroupCountByRole();

        // Get detailed group assignments
        $assignments = $panelMember->groups()
            ->with('schoolYear')
            ->withPivot('role')
            ->get()
            ->map(function ($group) {
                return [
                    'group_id' => $group->id,
                    'project_title' => $group->project_title ?? 'Group #' . $group->id,
                    'school_year' => $group->schoolYear->year ?? 'N/A',
                    'semester' => $group->schoolYear->semester ?? 1,
                    'role' => $group->pivot->role,
                    'cap_stage' => $group->cap_stage,
                    'defense_status' => $group->defense_status,
                    'is_complete' => $group->isComplete(),
                    'has_grades' => $group->evaluations()->exists(),
                ];
            });

        return response()->json([
            'member' => [
                'id' => $panelMember->id,
                'email' => $panelMember->email,
                'specialization' => $panelMember->specialization,
            ],
            'etl' => $totalEtl,
            'groups_by_role' => $groupsByRole,
            'assignments' => $assignments,
        ]);
    }

    /**
     * Show role values configuration
     */
    public function roleValues()
    {
        $roleValues = EtlRoleValue::all();
        return view('etl.role-values', compact('roleValues'));
    }

    /**
     * Update role values
     */
    public function updateRoleValues(Request $request)
    {
        $request->validate([
            'values' => 'required|array',
            'values.*.id' => 'required|exists:etl_role_values,id',
            'values.*.etl_value' => 'required|numeric|min:0|max:99.99',
        ]);

        foreach ($request->values as $value) {
            EtlRoleValue::where('id', $value['id'])->update([
                'etl_value' => $value['etl_value'],
            ]);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'ETL role values updated successfully.',
                'values' => EtlRoleValue::orderByRaw("FIELD(role, 'Adviser', 'Chair', 'Critique')")->get()
            ]);
        }

        return redirect()->route('etl.role-values')->with('success', 'ETL role values updated successfully.');
    }
}
