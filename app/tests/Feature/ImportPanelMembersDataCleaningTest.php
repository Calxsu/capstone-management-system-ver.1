<?php

namespace Tests\Feature;

use App\Models\PanelMember;
use App\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportPanelMembersDataCleaningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function panel_members_import_with_data_cleaning_handles_missing_fields_and_duplicates()
    {
        $sy = SchoolYear::factory()->create();

        $csv = <<<'CSV'
name,specialization,status
Dr. Juan Dela Cruz,Networking,active
Prof. Maria Santos,Systems Development,
Prof. Grace Hopper,Invalid Spec,inactive
Prof. Grace Hopper,Networking,active
,,active
CSV;

        $file = UploadedFile::fake()->createWithContent('panel_members_cleaning.csv', $csv);

        $response = $this->postJson('/api/import', [
            'file' => $file,
            'school_year_id' => $sy->id,
            'type' => 'panel_members',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(5, $data['rows_parsed']);
        // 3 valid unique names -> 3 imported (duplicate Grace Hopper skipped, empty name skipped)
        $this->assertEquals(3, $data['panel_members_imported']);

        $this->assertDatabaseHas('panel_members', ['name' => 'Dr. Juan Dela Cruz', 'specialization' => 'Networking', 'status' => 'active']);
        $pm = PanelMember::where('name', 'Dr. Juan Dela Cruz')->first();
        $this->assertNotNull($pm);
        // Email should not be set
        $this->assertNull($pm->email);

        // Maria Santos: missing status -> defaults to active
        $this->assertDatabaseHas('panel_members', ['name' => 'Prof. Maria Santos', 'specialization' => 'Systems Development', 'status' => 'active']);

        // Grace Hopper: invalid spec -> defaults to Networking, but duplicate skipped
        $this->assertDatabaseMissing('panel_members', ['name' => 'Prof. Grace Hopper']);
    }
}
