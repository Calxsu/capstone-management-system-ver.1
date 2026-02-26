<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolYear;
use App\Models\PanelMember;
use App\Models\Group;

class SimpleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create panel members
        $panel1 = PanelMember::create([
            'email' => 'john.doe@clsu2.edu.ph',
            'specialization' => 'Networking',
            'status' => 'active'
        ]);
        
        $panel2 = PanelMember::create([
            'email' => 'jane.smith@clsu2.edu.ph',
            'specialization' => 'Systems Development',
            'status' => 'active'
        ]);
        
        // Get first school year
        $schoolYear = SchoolYear::first();
        
        // Create groups
        $group1 = Group::create([
            'school_year_id' => $schoolYear->id,
            'title_optional' => 'Sample Project 1',
            'cap_status' => 'CAP1'
        ]);
        
        $group2 = Group::create([
            'school_year_id' => $schoolYear->id,
            'title_optional' => 'Sample Project 2',
            'cap_status' => 'CAP2'
        ]);
        
        // Assign panel members to groups
        $group1->panelMembers()->attach($panel1->id, ['role' => 'Adviser']);
        $group1->panelMembers()->attach($panel2->id, ['role' => 'Chair']);
        $group2->panelMembers()->attach($panel1->id, ['role' => 'Critique']);
        $group2->panelMembers()->attach($panel2->id, ['role' => 'Adviser']);
        
        $this->command->info('Simple data seeded successfully!');
    }
}
