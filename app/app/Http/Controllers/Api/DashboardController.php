<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\PanelMember;
use App\Models\Student;
use App\Models\Group;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        $totalSchoolYears = SchoolYear::count();
        $totalStudents = Student::count();
        $totalPanelMembers = PanelMember::count();
        $totalGroups = Group::count();
        $totalEvaluations = Evaluation::count();

        // CAP Progress
        $capStats = DB::table('groups')
            ->selectRaw('cap_stage, COUNT(*) as count')
            ->groupBy('cap_stage')
            ->pluck('count', 'cap_stage')
            ->toArray();

        // Average grades by CAP
        $avgGrades = DB::table('evaluations')
            ->selectRaw('cap_stage, AVG(grade) as average')
            ->groupBy('cap_stage')
            ->pluck('average', 'cap_stage')
            ->toArray();

        // Recent evaluations
        $recentEvaluations = DB::table('evaluations')
            ->join('groups', 'evaluations.group_id', '=', 'groups.id')
            ->select('evaluations.*', 'groups.project_title as group_name')
            ->orderBy('evaluations.created_at', 'desc')
            ->limit(5)
            ->get();

        // Active groups (with activity in last 30 days)
        $activeGroupsCount = DB::table('evaluations')
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('group_id')
            ->count('group_id');

        return response()->json([
            'schoolYears' => $totalSchoolYears,
            'students' => $totalStudents,
            'panelMembers' => $totalPanelMembers,
            'groups' => $totalGroups,
            'evaluations' => $totalEvaluations,
            'capProgress' => [
                'cap1' => $capStats[1] ?? 0,
                'cap2' => $capStats[2] ?? 0,
            ],
            'averageGrades' => [
                'cap1' => round($avgGrades[1] ?? 0, 1),
                'cap2' => round($avgGrades[2] ?? 0, 1),
            ],
            'recentEvaluations' => $recentEvaluations,
            'activeGroups' => $activeGroupsCount,
            'completionRate' => $totalGroups > 0 ? round(($capStats[2] ?? 0) / $totalGroups * 100, 1) : 0,
        ]);
    }

    /**
     * Get recent activity
     */
    public function recentActivity()
    {
        $activities = [];

        // Recent evaluations
        $recentEvals = DB::table('evaluations')
            ->join('groups', 'evaluations.group_id', '=', 'groups.id')
            ->select('evaluations.created_at', 'groups.project_title as group_name', 'evaluations.cap_stage', 'evaluations.grade')
            ->orderBy('evaluations.created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentEvals as $eval) {
            $activities[] = [
                'type' => 'evaluation',
                'message' => "{$eval->group_name} evaluated for CAPSTONE {$eval->cap_stage}",
                'detail' => "Grade: {$eval->grade}",
                'timestamp' => $eval->created_at,
            ];
        }

        // Recent group creations
        $recentGroups = DB::table('groups')
            ->select('created_at', 'project_title as name')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentGroups as $group) {
            $activities[] = [
                'type' => 'group',
                'message' => "New group created: {$group->name}",
                'detail' => '',
                'timestamp' => $group->created_at,
            ];
        }

        // Sort by timestamp
        usort($activities, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return response()->json(array_slice($activities, 0, 8));
    }
}
