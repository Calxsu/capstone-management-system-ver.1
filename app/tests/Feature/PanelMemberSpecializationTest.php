<?php

namespace Tests\Feature;

use App\Models\PanelMember;
use App\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PanelMemberSpecializationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function import_panel_member_with_specialization_is_stored()
    {
        $sy = SchoolYear::factory()->create();

        $csv = "name,specialization,status\n";
        $csv .= "Dr. John Import,Networking,active\n";

        $file = UploadedFile::fake()->createWithContent('panel_members.csv', $csv);

        $response = $this->postJson('/api/import', [
            'file' => $file,
            'school_year_id' => $sy->id,
            'type' => 'panel_members',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('panel_members', [
            'name' => 'Dr. John Import',
            'specialization' => 'Networking',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function panel_member_specialization_is_displayed_on_panel_members_index()
    {
        $member = PanelMember::create([
            'name' => 'Dr. Display Test',
            'specialization' => 'Systems Development',
            'status' => 'active',
        ]);

        $response = $this->get(route('panel-members.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Systems Development');
    }
}
