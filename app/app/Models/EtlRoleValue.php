<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtlRoleValue extends Model
{
    protected $fillable = [
        'role',
        'etl_value',
        'description',
    ];

    protected $casts = [
        'etl_value' => 'decimal:2',
    ];

    /**
     * Get ETL value for a specific role
     */
    public static function getValueForRole(string $role): float
    {
        $roleValue = self::where('role', $role)->first();
        return $roleValue ? (float) $roleValue->etl_value : 0.00;
    }

    /**
     * Get all role values as associative array
     */
    public static function getAllValues(): array
    {
        return self::pluck('etl_value', 'role')->toArray();
    }
}
