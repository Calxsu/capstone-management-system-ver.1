<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportHeaderOnlyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function header_only_csv_import_returns_helpful_error()
    {
        $sy = SchoolYear::factory()->create();

        $csv = "name,specialization,status\n"; // header only
        $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

        $response = $this->postJson('/api/import', [
            'file' => $file,
            'school_year_id' => $sy->id,
            'type' => 'students',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Import failed — file appears to contain no data or is malformed'
        ]);
        $this->assertStringContainsString('No data rows found', $response->json('error'));
    }
}
