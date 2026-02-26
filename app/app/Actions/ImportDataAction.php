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
use Illuminate\Support\Str;

class ImportDataAction
{
    // Store pending imports for preview/confirm flow
    private static array $pendingImports = [];

    public function __construct(
        private SchoolYearRepositoryInterface $schoolYearRepository,
        private StudentRepositoryInterface $studentRepository,
        private PanelMemberRepositoryInterface $panelMemberRepository
    ) {}

    /**
     * Preview data without saving to database.
     */
    public function preview(UploadedFile $file, int $schoolYearId, string $importType = 'students'): array
    {
        $parsed = $this->parseCsvFile($file);
        $header = array_map('trim', $parsed['header']);
        $data = $parsed['rows'];

        // Verify school year exists
        $schoolYear = $this->schoolYearRepository->find($schoolYearId);
        if (!$schoolYear) {
            throw new \Exception("School year not found");
        }

        $results = [
            'school_year' => $schoolYear,
            'rows_parsed' => count($data),
            'preview' => [
                'students' => [],
                'panel_members' => [],
                'groups' => [],
            ],
            'duplicates' => [],
            'errors' => []
        ];

        // Process data for preview
        foreach ($data as $rowIndex => $row) {
            try {
                if ($importType === 'students') {
                    $this->previewStudentRow($row, $results);
                } elseif ($importType === 'panel_members') {
                    $this->previewPanelMemberRow($row, $header, $results);
                } elseif ($importType === 'groups') {
                    $this->previewGroupRow($row, $header, $schoolYear, $results);
                }
            } catch (\Exception $e) {
                $results['errors'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }

        // Generate preview token for confirm/cancel
        $previewToken = Str::random(32);
        self::$pendingImports[$previewToken] = [
            'data' => $results,
            'created_at' => now(),
            'expires_at' => now()->addHour(),
        ];

        $results['preview_token'] = $previewToken;

        return $results;
    }

    /**
     * Confirm import after preview (commits the transaction).
     */
    public function confirm(string $previewToken, int $schoolYearId, string $importType = 'students'): array
    {
        if (!isset(self::$pendingImports[$previewToken])) {
            throw new \RuntimeException('Preview token not found or expired');
        }

        $pendingData = self::$pendingImports[$previewToken];
        
        // Check if expired
        if ($pendingData['expires_at']->isPast()) {
            unset(self::$pendingImports[$previewToken]);
            throw new \RuntimeException('Preview has expired');
        }

        $previewData = $pendingData['data'];

        DB::beginTransaction();
        try {
            $results = [
                'school_year' => $previewData['school_year'],
                'rows_parsed' => $previewData['rows_parsed'],
                'students' => [],
                'panel_members' => [],
                'groups' => [],
                'errors' => $previewData['errors'] ?? []
            ];

            // Process and save preview data
            if ($importType === 'students') {
                foreach ($previewData['preview']['students'] as $studentData) {
                    try {
                        $student = $this->studentRepository->create($studentData);
                        $results['students'][] = $student;
                    } catch (\Exception $e) {
                        $results['errors'][] = "Failed to create student: " . $e->getMessage();
                    }
                }
            } elseif ($importType === 'panel_members') {
                foreach ($previewData['preview']['panel_members'] as $panelData) {
                    try {
                        $panel = $this->panelMemberRepository->create($panelData);
                        $results['panel_members'][] = $panel;
                    } catch (\Exception $e) {
                        $results['errors'][] = "Failed to create panel member: " . $e->getMessage();
                    }
                }
            } elseif ($importType === 'groups') {
                foreach ($previewData['preview']['groups'] as $groupData) {
                    try {
                        $group = $this->saveGroupFromPreview($groupData, $previewData['school_year']);
                        $results['groups'][] = $group;
                    } catch (\Exception $e) {
                        $results['errors'][] = "Failed to create group: " . $e->getMessage();
                    }
                }
            }

            DB::commit();

            // Clear pending import
            unset(self::$pendingImports[$previewToken]);

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel import (clear pending preview).
     */
    public function cancel(string $previewToken): bool
    {
        if (isset(self::$pendingImports[$previewToken])) {
            unset(self::$pendingImports[$previewToken]);
            return true;
        }
        return false;
    }

    /**
     * Execute import directly (legacy method - still works).
     */
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
        if (!$handle) {
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
            throw new \RuntimeException('No data rows found in the uploaded file. Possible causes: file only contains headers, wrong delimiter (use comma), or encoding issues.');
        }

        return [
            'header' => $header,
            'rows' => $data,
        ];
    }

    /**
     * Preview student row - check duplicates and prepare data.
     */
    private function previewStudentRow(array $row, array &$results): void
    {
        if (count($row) < 1) {
            throw new \Exception("Invalid row format: expected at least 1 column (name)");
        }

        $name = trim($row[0] ?? '');
        $specializationName = trim($row[1] ?? '');

        if (empty($name)) {
            throw new \Exception("Name is required");
        }

        // Check if student already exists by name
        $existingStudent = Student::where('name', $name)->first();
        if ($existingStudent) {
            $results['duplicates'][] = [
                'type' => 'student',
                'identifier' => $name,
                'message' => "Student '{$name}' already exists"
            ];
            return;
        }

        // Normalize specialization
        $specialization = null;
        if (!empty($specializationName)) {
            $specializationName = trim($specializationName);
            if (stripos($specializationName, 'network') !== false) {
                $specialization = 'Networking';
            } elseif (stripos($specializationName, 'system') !== false || stripos($specializationName, 'development') !== false) {
                $specialization = 'Systems Development';
            } else {
                $specialization = $specializationName;
            }
        }

        $results['preview']['students'][] = [
            'name' => $name,
            'specialization' => $specialization,
            'status' => 'active',
        ];
    }

    /**
     * Preview panel member row - check duplicates and prepare data.
     */
    private function previewPanelMemberRow(array $row, array $header, array &$results): void
    {
        // Map values by header if provided
        $map = [];
        if (is_array($header) && count($header) > 0) {
            foreach ($header as $i => $key) {
                $map[strtolower(trim($key))] = trim($row[$i] ?? '');
            }
        }

        $email = trim($map['email'] ?? $row[0] ?? '');
        $specialization = trim($map['specialization'] ?? $row[1] ?? 'Networking');

        if (empty($email)) {
            throw new \Exception("Email is required");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format '{$email}'");
        }

        // Check for duplicates by email
        $existingPanel = PanelMember::where('email', $email)->first();
        if ($existingPanel) {
            $results['duplicates'][] = [
                'type' => 'panel_member',
                'identifier' => $email,
                'message' => "Panel member '{$email}' already exists"
            ];
            return;
        }

        // Validate specialization
        $validSpecializations = ['Networking', 'Systems Development'];
        if (!in_array($specialization, $validSpecializations)) {
            $specialization = 'Networking';
        }

        $results['preview']['panel_members'][] = [
            'email' => $email,
            'specialization' => $specialization,
            'status' => 'active',
            'etl_base' => 0,
        ];
    }

    /**
     * Preview group row - check duplicates and prepare data.
     */
    private function previewGroupRow(array $row, array $header, SchoolYear $schoolYear, array &$results): void
    {
        $map = [];
        foreach ($header as $i => $key) {
            $map[strtolower(trim($key))] = trim($row[$i] ?? '');
        }

        $studentNamesStr = trim($map['name of students per group'] ?? $row[0] ?? '');
        $chairEmail = trim($map['advisee/chair (clsu2 email)'] ?? $row[1] ?? '');
        $panel1Email = trim($map['panel 1 (clsu2 email)'] ?? $row[2] ?? '');
        $panel2Email = trim($map['panel 2 (clsu2 email)'] ?? $row[3] ?? '');
        $capstoneLevel = trim($map['capstone level'] ?? $row[4] ?? 'Capstone 1');

        if (empty($studentNamesStr)) {
            throw new \Exception("Name of Students per Group is required");
        }
        if (empty($chairEmail)) {
            throw new \Exception("Advisee/Chair email is required");
        }

        // Normalize capstone level
        $capstoneLevel = strtolower($capstoneLevel);
        if (strpos($capstoneLevel, '1') !== false) {
            $capstoneLevel = 'CAP1';
        } elseif (strpos($capstoneLevel, '2') !== false) {
            $capstoneLevel = 'CAP2';
        } else {
            $capstoneLevel = 'CAP1';
        }

        // Check for duplicate groups (same students, same school year)
        $studentNames = array_map('trim', explode(',', $studentNamesStr));
        $studentNames = array_filter($studentNames);
        sort($studentNames);
        
        // Check if similar group exists
        $existingGroups = \App\Models\Group::where('school_year_id', $schoolYear->id)->get();
        foreach ($existingGroups as $existingGroup) {
            $existingStudents = $existingGroup->students()->pluck('students.name')->toArray();
            sort($existingStudents);
            if ($existingStudents === $studentNames) {
                $results['duplicates'][] = [
                    'type' => 'group',
                    'identifier' => implode(', ', $studentNames),
                    'message' => "Group with similar students already exists"
                ];
                return;
            }
        }

        $groupData = [
            'student_names' => $studentNamesStr,
            'chair_email' => $chairEmail,
            'panel1_email' => $panel1Email,
            'panel2_email' => $panel2Email,
            'capstone_level' => $capstoneLevel,
        ];

        $results['preview']['groups'][] = $groupData;
    }

    /**
     * Save group from preview data.
     */
    private function saveGroupFromPreview(array $groupData, SchoolYear $schoolYear): \App\Models\Group
    {
        // Create group
        $group = \App\Models\Group::create([
            'school_year_id' => $schoolYear->id,
            'cap_status' => $groupData['capstone_level'],
            'title_optional' => null,
        ]);

        // Process students
        $studentNames = array_map('trim', explode(',', $groupData['student_names']));
        $studentNames = array_filter($studentNames);
        foreach ($studentNames as $studentName) {
            $student = \App\Models\Student::where('name', $studentName)->first();
            if (!$student) {
                $student = $this->studentRepository->create([
                    'name' => $studentName,
                    'specialization' => 'Networking',
                    'status' => 'active',
                ]);
            }
            $group->students()->attach($student->id);
        }

        // Process chair/adviser
        $chair = $this->getOrCreatePanelMember($groupData['chair_email']);
        $group->panelMembers()->attach($chair->id, ['role' => 'Adviser']);
        $group->panelMembers()->attach($chair->id, ['role' => 'Chair']);

        // Process Panel 1
        if (!empty($groupData['panel1_email'])) {
            $panel1 = $this->getOrCreatePanelMember($groupData['panel1_email']);
            $group->panelMembers()->attach($panel1->id, ['role' => 'Critique']);
        }

        // Process Panel 2
        if (!empty($groupData['panel2_email'])) {
            $panel2 = $this->getOrCreatePanelMember($groupData['panel2_email']);
            $group->panelMembers()->attach($panel2->id, ['role' => 'Critique']);
        }

        return $group;
    }

    private function getOrCreatePanelMember(string $email): PanelMember
    {
        $panelMember = \App\Models\PanelMember::where('email', $email)->first();
        
        if (!$panelMember) {
            $panelMember = $this->panelMemberRepository->create([
                'email' => $email,
                'specialization' => 'Networking',
                'status' => 'active',
                'etl_base' => 0,
            ]);
        }

        return $panelMember;
    }

    /**
     * Process a student row from CSV (legacy method).
     */
    private function processStudentRow(array $row, SchoolYear $schoolYear, array &$results): void
    {
        if (count($row) < 1) {
            throw new \Exception("Invalid row format: expected at least 1 column (name)");
        }

        $name = trim($row[0] ?? '');
        $specializationName = trim($row[1] ?? '');

        if (empty($name)) {
            throw new \Exception("Name is required");
        }

        // Check if student already exists by name
        $existingStudent = Student::where('name', $name)->first();
        if ($existingStudent) {
            throw new \Exception("Student '{$name}' already exists");
        }

        // Find specialization
        $specialization = null;
        if (!empty($specializationName)) {
            $specializationName = trim($specializationName);
            if (stripos($specializationName, 'network') !== false) {
                $specialization = 'Networking';
            } elseif (stripos($specializationName, 'system') !== false || stripos($specializationName, 'development') !== false) {
                $specialization = 'Systems Development';
            } else {
                $specialization = $specializationName;
            }
        }

        $student = $this->studentRepository->create([
            'name' => $name,
            'specialization' => $specialization,
            'status' => 'active',
        ]);

        $results['students'][] = $student;
    }

    /**
     * Process a panel member row from CSV (legacy method).
     */
    private function processPanelMemberRow(array $row, array $header, array &$results): void
    {
        $map = [];
        if (is_array($header) && count($header) > 0) {
            foreach ($header as $i => $key) {
                $map[strtolower(trim($key))] = trim($row[$i] ?? '');
            }
        }

        $email = trim($map['email'] ?? $row[0] ?? '');
        $specialization = trim($map['specialization'] ?? $row[1] ?? 'Networking');

        if (empty($email)) {
            $results['errors'][] = 'Skipped row: email is required';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $results['errors'][] = "Skipped row: invalid email format '{$email}'";
            return;
        }

        $validSpecializations = ['Networking', 'Systems Development'];
        if (!in_array($specialization, $validSpecializations)) {
            $specialization = 'Networking';
        }

        $existingPanel = PanelMember::where('email', $email)->first();
        if ($existingPanel) {
            $results['errors'][] = "Duplicate skipped: {$email} (already exists)";
            return;
        }

        $payload = [
            'email' => $email,
            'specialization' => $specialization,
            'status' => 'active',
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
     * Process a group row from CSV (legacy method).
     */
    private function processGroupRow(array $row, array $header, SchoolYear $schoolYear, array &$results): void
    {
        $map = [];
        foreach ($header as $i => $key) {
            $map[strtolower(trim($key))] = trim($row[$i] ?? '');
        }

        $studentNamesStr = trim($map['name of students per group'] ?? $row[0] ?? '');
        $chairEmail = trim($map['advisee/chair (clsu2 email)'] ?? $row[1] ?? '');
        $panel1Email = trim($map['panel 1 (clsu2 email)'] ?? $row[2] ?? '');
        $panel2Email = trim($map['panel 2 (clsu2 email)'] ?? $row[3] ?? '');
        $capstoneLevel = trim($map['capstone level'] ?? $row[4] ?? 'CAP1');

        if (empty($studentNamesStr)) {
            throw new \Exception("Name of Students per Group is required");
        }
        if (empty($chairEmail)) {
            throw new \Exception("Advisee/Chair email is required");
        }

        // Normalize capstone level
        $capstoneLevel = strtoupper($capstoneLevel);
        if (!in_array($capstoneLevel, ['CAP1', 'CAP2'])) {
            $capstoneLevel = 'CAP1';
        }

        $group = \App\Models\Group::create([
            'school_year_id' => $schoolYear->id,
            'cap_status' => $capstoneLevel,
            'title_optional' => null,
        ]);
        $results['groups'][] = $group;

        // Process students
        $studentNames = array_map('trim', explode(',', $studentNamesStr));
        $studentNames = array_filter($studentNames);
        foreach ($studentNames as $studentName) {
            $student = \App\Models\Student::where('name', $studentName)->first();
            if (!$student) {
                $student = $this->studentRepository->create([
                    'name' => $studentName,
                    'specialization' => 'Networking',
                    'status' => 'active',
                ]);
                $results['students'][] = $student;
            }
            $group->students()->attach($student->id);
        }

        // Process panels
        $getOrCreatePanelMember = function($email) use (&$results) {
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

        $chair = $getOrCreatePanelMember($chairEmail);
        $group->panelMembers()->attach($chair->id, ['role' => 'Adviser']);
        $group->panelMembers()->attach($chair->id, ['role' => 'Chair']);

        if (!empty($panel1Email)) {
            $panel1 = $getOrCreatePanelMember($panel1Email);
            $group->panelMembers()->attach($panel1->id, ['role' => 'Critique']);
        }

        if (!empty($panel2Email)) {
            $panel2 = $getOrCreatePanelMember($panel2Email);
            $group->panelMembers()->attach($panel2->id, ['role' => 'Critique']);
        }
    }
}
