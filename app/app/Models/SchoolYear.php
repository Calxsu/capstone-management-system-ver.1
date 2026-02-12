<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{
    protected $fillable = [
        'year',
        'semester',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get display name with semester
     */
    public function getDisplayNameAttribute(): string
    {
        $semesterText = $this->semester == 1 ? '1st Semester' : '2nd Semester';
        return "{$this->year} - {$semesterText}";
    }

    /**
     * Get short display name
     */
    public function getShortNameAttribute(): string
    {
        return "{$this->year} (Sem {$this->semester})";
    }
}
