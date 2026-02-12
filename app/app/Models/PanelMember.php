<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PanelMember extends Model
{
    protected $fillable = [
        'email',
        'specialization',
        'etl_base',
        'status',
    ];

    /**
     * Get the display name for the panel member (derived from email).
     */
    public function getNameAttribute(): string
    {
        // Extract name from email (e.g., john.doe@clsu2.edu.ph -> John Doe)
        $emailPrefix = explode('@', $this->email)[0];
        $nameParts = explode('.', $emailPrefix);
        return implode(' ', array_map('ucfirst', $nameParts));
    }

    protected $casts = [
        'etl_base' => 'decimal:2',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_panels')->withPivot('role')->withTimestamps();
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Get all roles this panel member has across all groups
     */
    public function getRolesAttribute(): array
    {
        return $this->groups()
            ->select('group_panels.role')
            ->distinct()
            ->pluck('role')
            ->toArray();
    }

    /**
     * Get group assignments with roles for a specific school year/semester
     */
    public function getAssignmentsForSemester(int $schoolYearId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->groups()
            ->where('school_year_id', $schoolYearId)
            ->withPivot('role')
            ->get();
    }

    /**
     * Calculate ETL for a specific semester (school year)
     * 
     * Rules:
     * - Each role assignment to a group earns ETL based on role value
     * - Adviser: 0.5, Chair: 0.3, Critique: 0.3
     * - Complete projects (those with grades) are NOT included in computation
     */
    public function calculateEtlForSemester(int $schoolYearId): array
    {
        $roleValues = EtlRoleValue::getAllValues();
        $etlBreakdown = [
            'Adviser' => ['count' => 0, 'value' => $roleValues['Adviser'] ?? 0.50, 'total' => 0],
            'Chair' => ['count' => 0, 'value' => $roleValues['Chair'] ?? 0.30, 'total' => 0],
            'Critique' => ['count' => 0, 'value' => $roleValues['Critique'] ?? 0.30, 'total' => 0],
        ];

        // Get all group assignments for this semester
        $assignments = $this->groups()
            ->where('school_year_id', $schoolYearId)
            ->withPivot('role')
            ->get();

        foreach ($assignments as $group) {
            // Skip completed projects (those with grades/evaluations)
            if ($group->isComplete()) {
                continue;
            }

            $role = $group->pivot->role;
            if (isset($etlBreakdown[$role])) {
                $etlBreakdown[$role]['count']++;
                $etlBreakdown[$role]['total'] += $etlBreakdown[$role]['value'];
            }
        }

        $totalEtl = array_sum(array_column($etlBreakdown, 'total'));

        return [
            'breakdown' => $etlBreakdown,
            'carry_over' => 0, // No carry-over concept with new rules
            'total' => $totalEtl,
        ];
    }

    /**
     * Calculate carry-over ETL from retained projects of previous semesters
     * Note: With new rules, only ongoing projects (no grades) are counted
     */
    public function calculateCarryOverEtl(int $currentSchoolYearId): float
    {
        // No carry-over with new ETL rules - only count ongoing projects
        return 0;
    }

    /**
     * Calculate total ETL across all semesters
     * Only counts ongoing projects (those without grades)
     */
    public function calculateTotalEtl(): array
    {
        $roleValues = EtlRoleValue::getAllValues();
        $semesterBreakdown = [];
        $grandTotal = 0;

        // Get all school years this panel member is involved in
        $schoolYearIds = $this->groups()
            ->select('school_year_id')
            ->distinct()
            ->pluck('school_year_id');

        foreach ($schoolYearIds as $schoolYearId) {
            $schoolYear = SchoolYear::find($schoolYearId);
            $semesterEtl = $this->calculateEtlForSemester($schoolYearId);
            
            $semesterBreakdown[] = [
                'school_year' => $schoolYear,
                'etl' => $semesterEtl,
            ];
            
            $grandTotal += $semesterEtl['total'];
        }

        return [
            'semesters' => $semesterBreakdown,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * Get count of ongoing groups (without grades) by role for this panel member
     */
    public function getGroupCountByRole(): array
    {
        // Get all groups for this panel member
        $groups = $this->groups()->withPivot('role')->get();
        
        $counts = [
            'Adviser' => 0,
            'Chair' => 0,
            'Critique' => 0,
        ];

        foreach ($groups as $group) {
            // Only count ongoing projects (without grades)
            if ($group->isOngoing()) {
                $role = $group->pivot->role;
                if (isset($counts[$role])) {
                    $counts[$role]++;
                }
            }
        }

        return $counts;
    }

    /**
     * Get count of ALL groups by role (including completed ones)
     */
    public function getAllGroupCountByRole(): array
    {
        return DB::table('group_panels')
            ->where('panel_member_id', $this->id)
            ->select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();
    }
}
