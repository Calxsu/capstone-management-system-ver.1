<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Display full recent activity feed.
     */
    public function recentActivity(Request $request)
    {
        $type = $request->string('type', 'all')->toString();
        $validTypes = ['all', 'evaluation', 'group', 'change'];

        if (!in_array($type, $validTypes, true)) {
            $type = 'all';
        }

        $allActivities = $this->buildRecentActivities();
        $filteredActivities = $allActivities;

        if ($type !== 'all') {
            $filteredActivities = $allActivities
                ->where('type', $type)
                ->values();
        }

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filteredActivities
            ->forPage($currentPage, $perPage)
            ->values();

        $activities = new LengthAwarePaginator(
            $currentItems,
            $filteredActivities->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $summary = [
            'all' => $allActivities->count(),
            'evaluation' => $allActivities->where('type', 'evaluation')->count(),
            'group' => $allActivities->where('type', 'group')->count(),
            'change' => $allActivities->where('type', 'change')->count(),
        ];

        return view('dashboard.recent-activity', [
            'activities' => $activities,
            'selectedType' => $type,
            'summary' => $summary,
        ]);
    }

    /**
     * Build merged activity stream from evaluations, group creations, and change logs.
     */
    private function buildRecentActivities(): Collection
    {
        $evaluationActivities = Evaluation::query()
            ->with(['group:id,project_title,title_optional', 'panelMember:id,email'])
            ->latest('created_at')
            ->limit(150)
            ->get()
            ->map(function (Evaluation $evaluation) {
                $groupName = $evaluation->group?->project_title
                    ?? $evaluation->group?->title_optional
                    ?? 'Untitled Group';

                return [
                    'type' => 'evaluation',
                    'title' => "{$groupName} evaluated for CAPSTONE {$evaluation->cap_stage}",
                    'detail' => 'Evaluator: ' . ($evaluation->panelMember?->email ?? 'N/A') . ', Grade: ' . number_format((float) $evaluation->grade, 2),
                    'timestamp' => $evaluation->created_at,
                    'meta' => [
                        'group' => $groupName,
                        'cap_stage' => $evaluation->cap_stage,
                        'grade' => (float) $evaluation->grade,
                    ],
                ];
            });

        $groupActivities = Group::query()
            ->select(['id', 'project_title', 'title_optional', 'created_at'])
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->map(function (Group $group) {
                $groupName = $group->project_title ?: ($group->title_optional ?: 'Untitled Group');

                return [
                    'type' => 'group',
                    'title' => "New group created: {$groupName}",
                    'detail' => 'Initial CAP stage: ' . ($group->cap_stage ?? 1),
                    'timestamp' => $group->created_at,
                    'meta' => [
                        'group' => $groupName,
                    ],
                ];
            });

        $changeActivities = DB::table('changes_log as changes')
            ->join('groups', 'groups.id', '=', 'changes.group_id')
            ->select([
                'changes.change_type',
                'changes.old_value',
                'changes.new_value',
                'changes.changed_by',
                'changes.timestamp',
                'groups.project_title',
                'groups.title_optional',
            ])
            ->orderByDesc('changes.timestamp')
            ->limit(150)
            ->get()
            ->map(function (object $change) {
                $groupName = $change->project_title ?: ($change->title_optional ?: 'Untitled Group');
                $detail = $this->formatChangeDetail($change->change_type, $change->old_value, $change->new_value);

                return [
                    'type' => 'change',
                    'title' => "Group update ({$change->change_type}): {$groupName}",
                    'detail' => $detail . ' | By: ' . ($change->changed_by ?: 'system'),
                    'timestamp' => Carbon::parse($change->timestamp),
                    'meta' => [
                        'group' => $groupName,
                        'change_type' => $change->change_type,
                    ],
                ];
            });

        return collect()
            ->merge($evaluationActivities)
            ->merge($groupActivities)
            ->merge($changeActivities)
            ->sortByDesc('timestamp')
            ->values();
    }

    private function formatChangeDetail(string $changeType, ?string $oldValue, ?string $newValue): string
    {
        $old = $this->decodeJsonText($oldValue);
        $new = $this->decodeJsonText($newValue);

        if ($changeType === 'student_added') {
            $studentName = $new['student_name'] ?? 'Unknown student';
            $reason = $new['reason'] ?? null;
            return $reason
                ? "Added {$studentName} ({$reason})"
                : "Added {$studentName}";
        }

        if ($changeType === 'student_removed') {
            $studentName = $old['student_name'] ?? 'Unknown student';
            $reason = $new['reason'] ?? null;
            return $reason
                ? "Removed {$studentName} ({$reason})"
                : "Removed {$studentName}";
        }

        return 'Updated group record';
    }

    private function decodeJsonText(?string $value): array
    {
        if (!$value) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
