<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\SchoolYear::create([
            'year' => 2024,
            'start_date' => '2024-08-01',
            'end_date' => '2025-05-31',
        ]);

        \App\Models\SchoolYear::create([
            'year' => 2025,
            'start_date' => '2025-08-01',
            'end_date' => '2026-05-31',
        ]);
    }
}
