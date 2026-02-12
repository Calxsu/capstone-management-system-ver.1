<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\PanelMember;
use App\Models\Group;
use App\Models\Evaluation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Evaluation::truncate();
        DB::table('group_students')->truncate();
        DB::table('group_panels')->truncate();
        DB::table('group_checklists')->truncate();
        Group::truncate();
        Student::truncate();
        PanelMember::truncate();
        SchoolYear::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create School Years with Semesters
        $sy2024_1st = SchoolYear::create([
            'year' => '2024-2025',
            'semester' => '1st Semester',
            'start_date' => '2024-06-01',
            'end_date' => '2024-10-31',
        ]);

        $sy2024_2nd = SchoolYear::create([
            'year' => '2024-2025',
            'semester' => '2nd Semester',
            'start_date' => '2024-11-01',
            'end_date' => '2025-03-31',
        ]);

        $sy2025_1st = SchoolYear::create([
            'year' => '2025-2026',
            'semester' => '1st Semester',
            'start_date' => '2025-06-01',
            'end_date' => '2025-10-31',
        ]);

        $sy2025_2nd = SchoolYear::create([
            'year' => '2025-2026',
            'semester' => '2nd Semester',
            'start_date' => '2025-11-01',
            'end_date' => '2026-03-31',
        ]);

        // Create Students with unique Filipino names
        $students = [
            ['name' => 'Renzo Alfredo Magbanua'],
            ['name' => 'Kristine Joy Evangelista'],
            ['name' => 'Mark Jethro Villanueva'],
            ['name' => 'Aileen Grace Montalbo'],
            ['name' => 'Bryan Kenneth Ocampo'],
            ['name' => 'Sharmaine Faye Dimaculangan'],
            ['name' => 'Joshua Andrei Pabalan'],
            ['name' => 'Patricia Mae Lumibao'],
            ['name' => 'Aldrin James Casimiro'],
            ['name' => 'Rhea Nicole Bagongahasa'],
            ['name' => 'Francis Jerome Tolentino'],
            ['name' => 'Denise Angela Macaraeg'],
            ['name' => 'Christian Dale Espiritu'],
            ['name' => 'Joanna Marie Custodio'],
            ['name' => 'Kurt Aldrich Pangilinan'],
            ['name' => 'Francine Ysabel Manansala'],
            ['name' => 'John Patrick Lazaro'],
            ['name' => 'Camille Andrea Dalisay'],
            ['name' => 'Nathan Lloyd Aguilar'],
            ['name' => 'Bianca Louise Sicat'],
            ['name' => 'Jericho Emmanuel Bondoc'],
            // Additional students for new CAPSTONE 1 groups (ungraded)
            ['name' => 'Rafael Antonio Mendoza'],
            ['name' => 'Angelica Rose Bautista'],
            ['name' => 'Jerome Kyle Tanglao'],
            ['name' => 'Mariel Joy Pangasinan'],
            ['name' => 'Vincent Carlo Delarosa'],
            ['name' => 'Stephanie Anne Guevarra'],
            ['name' => 'Adrian Joseph Calibuso'],
            ['name' => 'Trisha Mae Feliciano'],
            ['name' => 'Kenneth James Soriano'],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }

        // Create Panel Members with unique names
        $panelMembers = [
            ['name' => 'Dr. Ferdinand Magtanggol', 'status' => 'active'],
            ['name' => 'Dr. Remedios Lacsamana', 'status' => 'active'],
            ['name' => 'Prof. Arnel Buenaventura', 'status' => 'active'],
            ['name' => 'Dr. Ligaya Katipunan', 'status' => 'active'],
            ['name' => 'Prof. Virgilio Dimaano', 'status' => 'active'],
            ['name' => 'Dr. Corazon Mallari', 'status' => 'active'],
            ['name' => 'Prof. Rodolfo Capistrano', 'status' => 'active'],
            ['name' => 'Dr. Esperanza Villamor', 'status' => 'active'],
            ['name' => 'Prof. Leonardo Macatangay', 'status' => 'inactive'],
        ];

        foreach ($panelMembers as $panel) {
            PanelMember::create($panel);
        }

        // Create Groups with unique project titles
        $group1 = Group::create([
            'school_year_id' => $sy2024_1st->id,
            'project_title' => 'AgriSense: IoT-Based Crop Monitoring System for Rice Farmers',
            'cap_stage' => 2,
            'cap_status' => 'CAP2',
            'defense_status' => 'defended',
            'defense_date' => '2024-10-18',
        ]);

        $group2 = Group::create([
            'school_year_id' => $sy2024_1st->id,
            'project_title' => 'MedTrack: Pharmacy Inventory and Prescription Management',
            'cap_stage' => 2,
            'cap_status' => 'CAP2',
            'defense_status' => 'retained',
            'defense_date' => null,
        ]);

        $group3 = Group::create([
            'school_year_id' => $sy2024_1st->id,
            'project_title' => 'EcoRoute: Sustainable Transportation Planning App',
            'cap_stage' => 2,
            'cap_status' => 'CAP2',
            'defense_status' => 'defended',
            'defense_date' => '2024-10-22',
        ]);

        $group4 = Group::create([
            'school_year_id' => $sy2024_2nd->id,
            'project_title' => 'LearnHub: Adaptive E-Learning Platform with AI Tutoring',
            'cap_stage' => 1,
            'cap_status' => 'CAP1',
            'defense_status' => 'pending',
        ]);

        $group5 = Group::create([
            'school_year_id' => $sy2024_2nd->id,
            'project_title' => 'BarangayLink: Community Services Digital Portal',
            'cap_stage' => 1,
            'cap_status' => 'CAP1',
            'defense_status' => 'defended',
            'defense_date' => '2025-03-14',
        ]);

        $group6 = Group::create([
            'school_year_id' => $sy2025_1st->id,
            'project_title' => 'FreshMarket: Farm-to-Table Marketplace Platform',
            'cap_stage' => 2,
            'cap_status' => 'CAP2',
            'defense_status' => 'pending',
        ]);

        $group7 = Group::create([
            'school_year_id' => $sy2025_2nd->id,
            'project_title' => 'SafeGuard: Emergency Response and Disaster Preparedness App',
            'cap_stage' => 1,
            'cap_status' => 'CAP1',
            'defense_status' => 'pending',
        ]);

        // NEW CAPSTONE 1 GROUPS - NO GRADES YET
        $group8 = Group::create([
            'school_year_id' => $sy2025_2nd->id,
            'project_title' => 'PetPal: Pet Adoption and Care Management System',
            'cap_stage' => 1,
            'cap_status' => 'CAP1',
            'defense_status' => 'pending',
        ]);

        $group9 = Group::create([
            'school_year_id' => $sy2025_2nd->id,
            'project_title' => 'CampusEats: University Food Ordering Platform',
            'cap_stage' => 1,
            'cap_status' => 'CAP1',
            'defense_status' => 'pending',
        ]);

        $group10 = Group::create([
            'school_year_id' => $sy2025_2nd->id,
            'project_title' => 'StudyBuddy: Peer-to-Peer Tutoring Matchmaking App',
            'cap_stage' => 1,
            'cap_status' => 'CAP1',
            'defense_status' => 'pending',
        ]);

        // Assign Students to Groups (3 students per group)
        $group1->students()->attach([1, 2, 3]); // Renzo, Kristine, Mark
        $group2->students()->attach([4, 5, 6]); // Aileen, Bryan, Sharmaine
        $group3->students()->attach([7, 8, 9]); // Joshua, Patricia, Aldrin
        $group4->students()->attach([10, 11, 12]); // Rhea, Francis, Denise
        $group5->students()->attach([13, 14, 15]); // Christian, Joanna, Kurt
        $group6->students()->attach([16, 17, 18]); // Francine, John Patrick, Camille
        $group7->students()->attach([19, 20, 21]); // Nathan, Bianca, Jericho
        $group8->students()->attach([22, 23, 24]); // Rafael, Angelica, Jerome
        $group9->students()->attach([25, 26, 27]); // Mariel, Vincent, Stephanie
        $group10->students()->attach([28, 29, 30]); // Adrian, Trisha, Kenneth

        // Assign Panel Members to Groups (3 panelists per group with different roles)
        $group1->panelMembers()->attach([
            1 => ['role' => 'Adviser'],      // Dr. Ferdinand Magtanggol
            4 => ['role' => 'Chair'],        // Dr. Ligaya Katipunan
            6 => ['role' => 'Critique'],     // Dr. Corazon Mallari
        ]);

        $group2->panelMembers()->attach([
            2 => ['role' => 'Adviser'],      // Dr. Remedios Lacsamana
            5 => ['role' => 'Chair'],        // Prof. Virgilio Dimaano
            7 => ['role' => 'Critique'],     // Prof. Rodolfo Capistrano
        ]);

        $group3->panelMembers()->attach([
            3 => ['role' => 'Adviser'],      // Prof. Arnel Buenaventura
            8 => ['role' => 'Chair'],        // Dr. Esperanza Villamor
            1 => ['role' => 'Critique'],     // Dr. Ferdinand Magtanggol
        ]);

        $group4->panelMembers()->attach([
            4 => ['role' => 'Adviser'],      // Dr. Ligaya Katipunan
            2 => ['role' => 'Chair'],        // Dr. Remedios Lacsamana
            5 => ['role' => 'Critique'],     // Prof. Virgilio Dimaano
        ]);

        $group5->panelMembers()->attach([
            6 => ['role' => 'Adviser'],      // Dr. Corazon Mallari
            3 => ['role' => 'Chair'],        // Prof. Arnel Buenaventura
            8 => ['role' => 'Critique'],     // Dr. Esperanza Villamor
        ]);

        $group6->panelMembers()->attach([
            7 => ['role' => 'Adviser'],      // Prof. Rodolfo Capistrano
            1 => ['role' => 'Chair'],        // Dr. Ferdinand Magtanggol
            4 => ['role' => 'Critique'],     // Dr. Ligaya Katipunan
        ]);

        $group7->panelMembers()->attach([
            8 => ['role' => 'Adviser'],      // Dr. Esperanza Villamor
            6 => ['role' => 'Chair'],        // Dr. Corazon Mallari
            2 => ['role' => 'Critique'],     // Dr. Remedios Lacsamana
        ]);

        $group8->panelMembers()->attach([
            1 => ['role' => 'Adviser'],      // Dr. Ferdinand Magtanggol
            3 => ['role' => 'Chair'],        // Prof. Arnel Buenaventura
            5 => ['role' => 'Critique'],     // Prof. Virgilio Dimaano
        ]);

        $group9->panelMembers()->attach([
            2 => ['role' => 'Adviser'],      // Dr. Remedios Lacsamana
            4 => ['role' => 'Chair'],        // Dr. Ligaya Katipunan
            7 => ['role' => 'Critique'],     // Prof. Rodolfo Capistrano
        ]);

        $group10->panelMembers()->attach([
            6 => ['role' => 'Adviser'],      // Dr. Corazon Mallari
            8 => ['role' => 'Chair'],        // Dr. Esperanza Villamor
            1 => ['role' => 'Critique'],     // Dr. Ferdinand Magtanggol
        ]);

        // Create Evaluations (group level, not student level)
        // Groups 1-3: CAPSTONE 1 & 2 completed
        $this->createGroupEvaluations($group1, 1, [87.5, 89.0, 85.5]);
        $this->createGroupEvaluations($group1, 2, [91.0, 93.5, 88.0]);
        
        $this->createGroupEvaluations($group2, 1, [82.0, 84.5, 80.0]);
        $this->createGroupEvaluations($group2, 2, [85.0, 86.5, 83.0]);
        
        $this->createGroupEvaluations($group3, 1, [90.0, 88.5, 92.0]);
        $this->createGroupEvaluations($group3, 2, [94.0, 91.5, 95.0]);

        // Groups 4-5: CAPSTONE 1 completed
        $this->createGroupEvaluations($group4, 1, [78.5, 81.0, 76.5]);
        $this->createGroupEvaluations($group5, 1, [86.0, 88.5, 84.0]);

        // Group 6: CAPSTONE 1 & 2 completed
        $this->createGroupEvaluations($group6, 1, [89.0, 91.5, 87.0]);
        $this->createGroupEvaluations($group6, 2, [92.5, 94.0, 90.5]);

        // Groups 7-10: No evaluations yet (new CAPSTONE 1 groups - ungraded)

        $this->command->info('');
        $this->command->info('🎉 Fresh sample data seeded successfully!');
        $this->command->info('');
        $this->command->info('📊 Created:');
        $this->command->info('   • 4 School Years (2024-2025 & 2025-2026, both semesters)');
        $this->command->info('   • 30 Students');
        $this->command->info('   • 9 Panel Members');
        $this->command->info('   • 10 Capstone Groups');
        $this->command->info('');
        $this->command->info('📋 Groups Summary:');
        $this->command->info('   1. AgriSense (IoT Crop Monitoring) - DEFENDED ✅');
        $this->command->info('   2. MedTrack (Pharmacy Inventory) - RETAINED 🔄');
        $this->command->info('   3. EcoRoute (Transportation App) - DEFENDED ✅');
        $this->command->info('   4. LearnHub (E-Learning Platform) - CAP1 GRADED');
        $this->command->info('   5. BarangayLink (Community Portal) - DEFENDED ✅');
        $this->command->info('   6. FreshMarket (Marketplace) - CAPSTONE 2 PENDING');
        $this->command->info('   7. SafeGuard (Emergency App) - CAP1 NO GRADE ⏳');
        $this->command->info('   8. PetPal (Pet Adoption System) - CAP1 NO GRADE ⏳');
        $this->command->info('   9. CampusEats (Food Ordering) - CAP1 NO GRADE ⏳');
        $this->command->info('   10. StudyBuddy (Tutoring App) - CAP1 NO GRADE ⏳');
        $this->command->info('');
    }

    private function createGroupEvaluations(Group $group, int $capStage, array $grades): void
    {
        $panelMembers = $group->panelMembers;
        $date = $capStage === 1 ? '2025-09-15' : '2025-12-10';

        foreach ($panelMembers as $index => $panel) {
            Evaluation::create([
                'group_id' => $group->id,
                'panel_member_id' => $panel->id,
                'student_id' => $group->students->first()->id, // Use first student as reference
                'cap_stage' => $capStage,
                'grade' => $grades[$index] ?? 80,
                'criteria' => 'Technical Implementation, Documentation, Presentation',
                'date' => $date,
            ]);
        }
    }
}
