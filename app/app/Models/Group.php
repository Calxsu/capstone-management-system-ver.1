<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'school_year_id',
        'project_title',
        'cap_stage',
        'cap_status',
        'title_optional',
        'defense_status',
        'defense_date',
    ];

    protected $casts = [
        'cap_stage' => 'integer',
        'defense_date' => 'date',
    ];

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'group_students');
    }

    public function panelMembers(): BelongsToMany
    {
        return $this->belongsToMany(PanelMember::class, 'group_panels')
            ->withPivot('role')
            ->withTimestamps()
            ->orderByRaw("CASE 
                WHEN group_panels.role = 'Adviser' THEN 1 
                WHEN group_panels.role = 'Chair' THEN 2 
                WHEN group_panels.role = 'Critique' THEN 3 
                ELSE 4 
            END");
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function changesLogs(): HasMany
    {
        return $this->hasMany(ChangesLog::class);
    }

    /**
     * Check if the group has successfully defended
     */
    public function isDefended(): bool
    {
        return $this->defense_status === 'defended';
    }

    /**
     * Check if the group is retained from previous semester
     */
    public function isRetained(): bool
    {
        return $this->defense_status === 'retained';
    }

    /**
     * Mark group as defended
     */
    public function markAsDefended(?string $date = null): void
    {
        $this->update([
            'defense_status' => 'defended',
            'defense_date' => $date ?? now(),
        ]);
    }

    /**
     * Mark group as retained (carry over to next semester)
     */
    public function markAsRetained(): void
    {
        $this->update([
            'defense_status' => 'retained',
        ]);
    }

    /**
     * Get the checklist entries for this group.
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(GroupChecklist::class);
    }

    /**
     * Get the completed checklist items for this group.
     */
    public function completedChecklists(): HasMany
    {
        return $this->hasMany(GroupChecklist::class)->where('is_completed', true);
    }

    /**
     * Check if the capstone project is complete (has grades/evaluations)
     * A complete project should NOT be included in ETL computation
     */
    public function isComplete(): bool
    {
        return $this->evaluations()->exists();
    }

    /**
     * Check if the project is still ongoing (no grades yet)
     * Only ongoing projects should be counted for ETL
     */
    public function isOngoing(): bool
    {
        return !$this->isComplete();
    }
}
