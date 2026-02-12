<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use Illuminate\Database\Seeder;

class ChecklistItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Final Defense',
                'description' => 'Successfully completed final CAPSTONE 2 defense',
                'order' => 1,
                'is_required' => true,
            ],
            [
                'name' => 'Approval Sheet Signed',
                'description' => 'All panel members have signed the approval sheet',
                'order' => 2,
                'is_required' => true,
            ],
            [
                'name' => 'Final Paper Submitted',
                'description' => 'Submitted soft copy of final paper to the department',
                'order' => 3,
                'is_required' => true,
            ],
            [
                'name' => 'Hard Bound Copy',
                'description' => 'Submitted hard bound copy of the thesis/capstone document',
                'order' => 4,
                'is_required' => true,
            ],
            [
                'name' => 'Hard Bound Receipt',
                'description' => 'Received and submitted hard bound receipt from printing',
                'order' => 5,
                'is_required' => true,
            ],
            [
                'name' => 'Source Code Submitted',
                'description' => 'Submitted complete source code and documentation',
                'order' => 6,
                'is_required' => true,
            ],
            [
                'name' => 'Library Clearance',
                'description' => 'Obtained clearance from the library',
                'order' => 7,
                'is_required' => false,
            ],
            [
                'name' => 'Department Clearance',
                'description' => 'Obtained clearance from the department',
                'order' => 8,
                'is_required' => false,
            ],
        ];

        foreach ($items as $item) {
            ChecklistItem::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
