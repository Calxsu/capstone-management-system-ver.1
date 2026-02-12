<?php

namespace App\Actions;

use App\Models\PanelMember;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Repositories\PanelMemberRepositoryInterface;
use App\Repositories\SchoolYearRepositoryInterface;
use App\Repositories\StudentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ImportDataAction
{
    public function __construct(
        private SchoolYearRepositoryInterface $schoolYearRepository,
        private StudentRepositoryInterface $studentRepository,
        private PanelMemberRepositoryInterface $panelMemberRepository
    ) {}

    public function execute(UploadedFile $file, int $schoolYearId, string $importType = 'students'): array
    {
        $parsed = $this->parseCsvFile($file);
        $header = array_map('trim', $parsed['header']);
        $data = $parsed['rows'];

        

        DB::beginTransaction();
        try {
            $results = [
                'school_year' => null,
                'rows_parsed' => count($data),
                'students' => [],
                'panel_members' => [],
                'groups' => [],
                'errors' => []
            ];

            // Verify school year exists
            $schoolYear = $this->schoolYearRepository->find($schoolYearId);
            if (!$schoolYear) {
                throw new \Exception("School year not found");
            }
            $results['school_year'] = $schoolYear;

            // Process data based on import type
            foreach ($data as $rowIndex => $row) {
                try {
                    if ($importType === 'students') {
                        $this->processStudentRow($row, $schoolYear, $results);
                    } elseif ($importType === 'panel_members') {
                        // pass header map so importer can map columns by name (flexible order)
                        $this->processPanelMemberRow($row, $header, $results);
                    } elseif ($importType === 'groups') {
                        $this->processGroupRow($row, $header, $schoolYear, $results);
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();
            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function parseCsvFile(UploadedFile $file): array
    {
        $path = $file->getPathname();
        $handle = fopen($path, 'r');
        if (! $handle) {
            throw new \RuntimeException('Unable to open uploaded file');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new \RuntimeException('Uploaded file is empty');
        }

        // Try to detect delimiter (comma, semicolon, tab)
        $possible = [',', ';', "\t"];
        $delimiter = ',';
        foreach ($possible as $d) {
            if (substr_count($firstLine, $d) >= 1) {
                $delimiter = $d;
                break;
            }
        }

        // Rewind and read as CSV using detected delimiter
        rewind($handle);

        // Read header
        $header = fgetcsv($handle, 0, $delimiter);
        if ($header === false) {
            fclose($handle);
            throw new \RuntimeException('Invalid CSV header');
        }

        $data = [];
        $rawRows = 0;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rawRows++;
            // Skip empty rows (all empty columns)
            if (empty(array_filter($row))) {
                continue;
            }
            $data[] = $row;
        }

        fclose($handle);

        if (count($data) === 0) {
            // No non-empty data rows found — give a helpful message
            throw new \RuntimeException('No data rows found in the uploaded file. Possible causes: file only contains headers, wrong delimiter (use comma), or encoding issues.');
        }

        // Return both header and parsed rows so callers can validate required columns
        return [
            'header' => $header,
            'rows' => $data,
        ];
    }

    /**
     * Process a student row from CSV
     * Expected format: name, specialization, status
     */
    private function processStudentRow(array $row, SchoolYear $schoolYear, array &$results): void
    {
        if (count($row) < 1) {
            throw new \Exception("Invalid row format: expected at least 1 column (name)");
        }

        $name = trim($row[0] ?? '');
        $specializationName = trim($row[1] ?? '');
        $status = strtolower(trim($row[2] ?? 'active'));

        if (empty($name)) {
            throw new \Exception("Name is required");
        }

        // Check if student already exists by name
        $existingStudent = Student::where('name', $name)->first();
        if ($existingStudent) {
            throw new \Exception("Student '{$name}' already exists");
        }

        // Find specialization - use the value directly as string
        $specialization = null;
        if (!empty($specializationName)) {
            // Normalize specialization name
            $specializationName = trim($specializationName);
            if (stripos($specializationName, 'network') !== false) {
                $specialization = 'Networking';
            } elseif (stripos($specializationName, 'system') !== false || stripos($specializationName, 'development') !== false) {
                $specialization = 'Systems Development';
            } else {
                $specialization = $specializationName;
            }
        }

        // Validate status
        if (!in_array($status, ['active', 'inactive', 'graduated'])) {
            $status = 'active';
        }

        // Create student
        $student = $this->studentRepository->create([
            'name' => $name,
            'specialization' => $specialization,
            'status' => $status,
        ]);

        $results['students'][] = $student;
    }

    /**
     * Process a panel member row from CSV
     * Expected format: name, specialization, status
     * Data cleaning: trims fields, provides defaults for missing columns, validates values, skips duplicates and invalid rows.
     */
    private function processPanelMemberRow(array $row, ?array $resultsHeaderMap = null, array &$results): void
    {
        // Support header-mapped rows or positional.
        // Expected: name, specialization, status

        // Map values by header if provided
        $map = [];
        if (is_array($resultsHeaderMap) && count($resultsHeaderMap) > 0) {
            foreach ($resultsHeaderMap as $i => $key) {
                $map[strtolower(trim($key))] = trim($row[$i] ?? '');
            }
        }

        // Extract fields (prefer header map, fall back to positional)
        $email = trim($map['email'] ?? $row[0] ?? '');
        $specialization = trim($map['specialization'] ?? $row[1] ?? 'Networking');
        $status = trim($map['status'] ?? $row[2] ?? 'active');

        // Skip if email is empty
        if (empty($email)) {
            $results['errors'][] = 'Skipped row: email is required';
            return;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $results['errors'][] = "Skipped row: invalid email format '{$email}'";
            return;
        }

        // Validate and normalize specialization
        $validSpecializations = ['Networking', 'Systems Development'];
        if (!in_array($specialization, $validSpecializations)) {
            $specialization = 'Networking'; // default
        }

        // Validate and normalize status
        $validStatuses = ['active', 'inactive'];
        if (!in_array(strtolower($status), $validStatuses)) {
            $status = 'active'; // default
        } else {
            $status = strtolower($status);
        }

        // Check for duplicates by email
        $existingPanel = PanelMember::where('email', $email)->first();
        if ($existingPanel) {
            $results['errors'][] = "Duplicate skipped: {$email} (already exists)";
            return;
        }

        // Prepare payload
        $payload = [
            'email' => $email,
            'specialization' => $specialization,
            'status' => $status,
            'etl_base' => 0,
        ];

        try {
            $panelMember = $this->panelMemberRepository->create($payload);
            $results['panel_members'][] = $panelMember;
        } catch (\Exception $e) {
            $results['errors'][] = 'DB error for ' . $email . ': ' . $e->getMessage();
        }
    }

    /**
     * Process a group row from CSV
     * Expected format: Name of Students per Group, Advisee/Chair (clsu2 email), Panel 1 (clsu2 email), Panel 2 (clsu2 email)
     */
    private function processGroupRow(array $row, array $header, SchoolYear $schoolYear, array &$results): void
    {
        // Map values by header (flexible column order)
        $map = [];
        foreach ($header as $i => $key) {
            $map[strtolower(trim($key))] = trim($row[$i] ?? '');
        }

        // Extract fields
        $studentNamesStr = trim($map['name of students per group'] ?? $row[0] ?? '');
        $chairEmail = trim($map['advisee/chair (clsu2 email)'] ?? $row[1] ?? '');
        $panel1Email = trim($map['panel 1 (clsu2 email)'] ?? $row[2] ?? '');
        $panel2Email = trim($map['panel 2 (clsu2 email)'] ?? $row[3] ?? '');
        $capStatus = trim($map['cap_status'] ?? $map['cap status'] ?? $row[4] ?? 'CAP1');

        // Validate required fields
        if (empty($studentNamesStr)) {
            throw new \Exception("Name of Students per Group is required");
        }
        if (empty($chairEmail)) {
            throw new \Exception("Advisee/Chair email is required");
        }

        // Validate cap status
        $capStatus = strtoupper($capStatus);
        if (!in_array($capStatus, ['CAP1', 'CAP2'])) {
            $capStatus = 'CAP1'; // Default to CAP1 if invalid
        }

        // Create group
        $group = \App\Models\Group::create([
            'school_year_id' => $schoolYear->id,
            'cap_status' => $capStatus,
            'title_optional' => null,
        ]);
        $results['groups'][] = $group;

        // Process students
        $studentNames = array_map('trim', explode(',', $studentNamesStr));
        $studentNames = array_filter($studentNames); // Remove empty names
        foreach ($studentNames as $studentName) {
            // Check if student exists
            $student = \App\Models\Student::where('name', $studentName)->first();
            
            // If student doesn't exist, create them
            if (!$student) {
                $student = $this->studentRepository->create([
                    'name' => $studentName,
                    'specialization' => 'Networking', // Default specialization
                    'status' => 'active',
                ]);
                $results['students'][] = $student;
            }

            // Add student to group
            $group->students()->attach($student->id);
        }

        // Helper function to get or create panel member from email
        $getOrCreatePanelMember = function($email) use (&$results) {
            // Check if panel member exists
            $panelMember = \App\Models\PanelMember::where('email', $email)->first();
            
            if (!$panelMember) {
                $panelMember = $this->panelMemberRepository->create([
                    'email' => $email,
                    'specialization' => 'Networking',
                    'status' => 'active',
                    'etl_base' => 0,
                ]);
                $results['panel_members'][] = $panelMember;
            }

            return $panelMember;
        };

        // Process chair/adviser (Adviser and Chair Panel)
        $chair = $getOrCreatePanelMember($chairEmail);
        $group->panelMembers()->attach($chair->id, ['role' => 'Adviser']);
        $group->panelMembers()->attach($chair->id, ['role' => 'Chair']);

        // Process Panel 1 (Critique)
        if (!empty($panel1Email)) {
            $panel1 = $getOrCreatePanelMember($panel1Email);
            $group->panelMembers()->attach($panel1->id, ['role' => 'Critique']);
        }

        // Process Panel 2 (Critique)
        if (!empty($panel2Email)) {
            $panel2 = $getOrCreatePanelMember($panel2Email);
            $group->panelMembers()->attach($panel2->id, ['role' => 'Critique']);
        }
    }
}