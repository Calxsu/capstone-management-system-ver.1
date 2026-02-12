<?php

namespace App\Http\Controllers\Api;

use App\Actions\ComputeETLAction;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Report;
use App\Models\SchoolYear;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ComputeETLAction $computeETLAction
    ) {}

    /**
     * Generate ETL report for all panel members.
     */
    public function etlReport(Request $request): JsonResponse
    {
        $schoolYearId = $request->query('school_year_id');

        $query = \App\Models\PanelMember::with(['groups' => function ($query) use ($schoolYearId) {
            if ($schoolYearId) {
                $query->where('school_year_id', $schoolYearId);
            }
        }]);

        $panelMembers = $query->get();

        $reportData = [
            'generated_at' => now(),
            'school_year' => $schoolYearId ? SchoolYear::find($schoolYearId)?->year : 'All Years',
            'panel_members' => [],
            'summary' => [
                'total_panel_members' => $panelMembers->count(),
                'total_etl' => 0,
                'average_etl' => 0,
            ]
        ];

        $totalETL = 0;
        foreach ($panelMembers as $panelMember) {
            $etl = $this->computeETLAction->execute($panelMember->id);

            $reportData['panel_members'][] = [
                'id' => $panelMember->id,
                'email' => $panelMember->email,
                'role' => $panelMember->role,
                'status' => $panelMember->status,
                'groups_assigned' => $panelMember->groups->count(),
                'etl_total' => $etl,
                'groups' => $panelMember->groups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'title' => $group->title_optional,
                        'cap_status' => $group->cap_status,
                        'students_count' => $group->students->count(),
                    ];
                }),
            ];

            $totalETL += $etl;
        }

        $reportData['summary']['total_etl'] = $totalETL;
        $reportData['summary']['average_etl'] = $panelMembers->count() > 0
            ? round($totalETL / $panelMembers->count(), 2)
            : 0;

        // Sort by ETL descending
        usort($reportData['panel_members'], function ($a, $b) {
            return $b['etl_total'] <=> $a['etl_total'];
        });

        // Save report to database
        $savedReport = Report::create([
            'school_year_id' => $schoolYearId ?: null, // Allow null for all years report
            'type' => 'etl_report',
            'data' => $reportData,
            'generated_at' => now(),
        ]);

        return response()->json([
            'report_id' => $savedReport->id,
            'data' => $reportData,
        ]);
    }

    /**
     * Generate CAP progress report.
     */
    public function capProgressReport(Request $request): JsonResponse
    {
        $schoolYearId = $request->query('school_year_id');

        $query = Group::with(['schoolYear', 'students', 'panelMembers', 'evaluations']);

        if ($schoolYearId) {
            $query->where('school_year_id', $schoolYearId);
        }

        $groups = $query->get();

        $reportData = [
            'generated_at' => now(),
            'school_year' => $schoolYearId ? SchoolYear::find($schoolYearId)?->year : 'All Years',
            'groups' => [],
            'summary' => [
                'total_groups' => $groups->count(),
                'cap1_groups' => 0,
                'cap2_groups' => 0,
                'total_students' => 0,
                'evaluated_students' => 0,
                'completion_percentage' => 0,
            ]
        ];

        foreach ($groups as $group) {
            $studentCount = $group->students->count();
            $evaluatedStudents = $group->evaluations()
                ->select('student_id')
                ->groupBy('student_id')
                ->get()
                ->count();

            $completionPercentage = $studentCount > 0
                ? round(($evaluatedStudents / $studentCount) * 100, 2)
                : 0;

            $groupData = [
                'id' => $group->id,
                'title' => $group->title_optional,
                'cap_status' => $group->cap_status,
                'school_year' => $group->schoolYear->year,
                'students_count' => $studentCount,
                'panel_members_count' => $group->panelMembers->count(),
                'evaluated_students' => $evaluatedStudents,
                'completion_percentage' => $completionPercentage,
                'students' => $group->students->map(function ($student) use ($group) {
                    $evaluations = $group->evaluations()->where('student_id', $student->id)->get();
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'evaluations_count' => $evaluations->count(),
                        'average_grade' => $evaluations->avg('grade'),
                    ];
                }),
            ];

            $reportData['groups'][] = $groupData;

            // Update summary
            if ($group->cap_status === 'CAP1') {
                $reportData['summary']['cap1_groups']++;
            } else {
                $reportData['summary']['cap2_groups']++;
            }

            $reportData['summary']['total_students'] += $studentCount;
            $reportData['summary']['evaluated_students'] += $evaluatedStudents;
        }

        $totalEvaluationsNeeded = $reportData['summary']['total_students'];
        $reportData['summary']['completion_percentage'] = $totalEvaluationsNeeded > 0
            ? round(($reportData['summary']['evaluated_students'] / $totalEvaluationsNeeded) * 100, 2)
            : 0;

        // Save report to database
        $savedReport = Report::create([
            'school_year_id' => $schoolYearId,
            'type' => 'cap_progress_report',
            'data' => $reportData,
            'generated_at' => now(),
        ]);

        return response()->json([
            'report_id' => $savedReport->id,
            'data' => $reportData,
        ]);
    }

    /**
     * Get saved reports.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Report::with('schoolYear');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        }

        $reports = $query->orderBy('generated_at', 'desc')->get();

        return response()->json($reports);
    }

    /**
     * Export ETL report as PDF.
     */
    public function exportETLReport(Request $request)
    {
        $schoolYearId = $request->query('school_year_id');

        $query = \App\Models\PanelMember::with(['groups' => function ($query) use ($schoolYearId) {
            if ($schoolYearId) {
                $query->where('school_year_id', $schoolYearId);
            }
        }]);

        $panelMembers = $query->get();

        $reportData = [
            'generated_at' => now(),
            'school_year' => $schoolYearId ? SchoolYear::find($schoolYearId)?->year : 'All Years',
            'panel_members' => [],
            'summary' => [
                'total_panel_members' => $panelMembers->count(),
                'total_etl' => 0,
                'average_etl' => 0,
            ]
        ];

        $totalETL = 0;
        foreach ($panelMembers as $panelMember) {
            $etl = $this->computeETLAction->execute($panelMember->id);

            $reportData['panel_members'][] = [
                'id' => $panelMember->id,
                'email' => $panelMember->email,
                'role' => $panelMember->role,
                'status' => $panelMember->status,
                'groups_assigned' => $panelMember->groups->count(),
                'etl_total' => $etl,
            ];

            $totalETL += $etl;
        }

        $reportData['summary']['total_etl'] = $totalETL;
        $reportData['summary']['average_etl'] = $panelMembers->count() > 0
            ? round($totalETL / $panelMembers->count(), 2)
            : 0;

        // Sort by ETL descending
        usort($reportData['panel_members'], function ($a, $b) {
            return $b['etl_total'] <=> $a['etl_total'];
        });

        $pdf = Pdf::loadView('reports.etl', compact('reportData'));

        return $pdf->download('etl_report_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Export CAP progress report as PDF.
     */
    public function exportCAPProgressReport(Request $request)
    {
        $schoolYearId = $request->query('school_year_id');

        $query = Group::with(['schoolYear', 'students', 'panelMembers', 'evaluations']);

        if ($schoolYearId) {
            $query->where('school_year_id', $schoolYearId);
        }

        $groups = $query->get();

        $reportData = [
            'generated_at' => now(),
            'school_year' => $schoolYearId ? SchoolYear::find($schoolYearId)?->year : 'All Years',
            'groups' => [],
            'summary' => [
                'total_groups' => $groups->count(),
                'cap1_groups' => 0,
                'cap2_groups' => 0,
                'total_students' => 0,
                'evaluated_students' => 0,
                'completion_percentage' => 0,
            ]
        ];

        foreach ($groups as $group) {
            $studentCount = $group->students->count();
            $evaluatedStudents = $group->evaluations()
                ->select('student_id')
                ->groupBy('student_id')
                ->get()
                ->count();

            $completionPercentage = $studentCount > 0
                ? round(($evaluatedStudents / $studentCount) * 100, 2)
                : 0;

            $groupData = [
                'id' => $group->id,
                'title' => $group->title_optional,
                'cap_status' => $group->cap_status,
                'school_year' => $group->schoolYear->year,
                'students_count' => $studentCount,
                'panel_members_count' => $group->panelMembers->count(),
                'evaluated_students' => $evaluatedStudents,
                'completion_percentage' => $completionPercentage,
            ];

            $reportData['groups'][] = $groupData;

            // Update summary
            if ($group->cap_status === 'CAP1') {
                $reportData['summary']['cap1_groups']++;
            } else {
                $reportData['summary']['cap2_groups']++;
            }

            $reportData['summary']['total_students'] += $studentCount;
            $reportData['summary']['evaluated_students'] += $evaluatedStudents;
        }

        $totalEvaluationsNeeded = $reportData['summary']['total_students'];
        $reportData['summary']['completion_percentage'] = $totalEvaluationsNeeded > 0
            ? round(($reportData['summary']['evaluated_students'] / $totalEvaluationsNeeded) * 100, 2)
            : 0;

        $pdf = Pdf::loadView('reports.cap_progress', compact('reportData'));

        return $pdf->download('cap_progress_report_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
}
