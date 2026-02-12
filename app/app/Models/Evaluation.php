<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    protected $fillable = [
        'group_id',
        'panel_member_id',
        'student_id',
        'cap_stage',
        'grade',
        'criteria',
        'remarks',
        'date',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'cap_stage' => 'integer',
        'date' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function panelMember(): BelongsTo
    {
        return $this->belongsTo(PanelMember::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
