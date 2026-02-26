<?php

namespace App\Http\Controllers\Api;

use App\Actions\ImportDataAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public function __construct(
        private ImportDataAction $importDataAction
    ) {}

    /**
     * Preview data from CSV/Excel file (without saving to database).
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'school_year_id' => 'required|integer|exists:school_years,id',
            'type' => 'required|string|in:students,panel_members,groups',
        ]);

        try {
            $file = $request->file('file');
            $schoolYearId = $request->input('school_year_id');
            $importType = $request->input('type');

            // Execute in preview mode - returns data without committing to DB
            $preview = $this->importDataAction->preview($file, $schoolYearId, $importType);

            return response()->json([
                'message' => 'Preview data retrieved successfully',
                'data' => [
                    'preview_token' => $preview['preview_token'] ?? null,
                    'school_year' => $preview['school_year']->year,
                    'rows_parsed' => $preview['rows_parsed'],
                    'preview' => $preview['preview'],
                    'duplicates' => $preview['duplicates'],
                    'errors' => $preview['errors'],
                    'summary' => [
                        'new_students' => count($preview['preview']['students'] ?? []),
                        'new_panel_members' => count($preview['preview']['panel_members'] ?? []),
                        'new_groups' => count($preview['preview']['groups'] ?? []),
                        'duplicates_count' => count($preview['duplicates'] ?? []),
                        'errors_count' => count($preview['errors'] ?? []),
                    ]
                ]
            ]);

        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => 'Preview failed',
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Preview failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Confirm import after preview (commits the transaction).
     */
    public function confirmImport(Request $request): JsonResponse
    {
        $request->validate([
            'preview_token' => 'required|string',
            'school_year_id' => 'required|integer|exists:school_years,id',
            'type' => 'required|string|in:students,panel_members,groups',
        ]);

        try {
            $schoolYearId = $request->input('school_year_id');
            $importType = $request->input('type');
            $previewToken = $request->input('preview_token');

            $results = $this->importDataAction->confirm($previewToken, $schoolYearId, $importType);

            $studentsImported = count($results['students']);
            $panelsImported = count($results['panel_members']);
            $groupsImported = count($results['groups']);
            $rowsParsed = $results['rows_parsed'] ?? null;
            $totalImported = $studentsImported + $panelsImported + $groupsImported;

            if (($rowsParsed !== null && $rowsParsed > 0) && $totalImported === 0) {
                return response()->json([
                    'message' => 'Import completed but no records were imported',
                    'data' => [
                        'school_year' => $results['school_year']->year,
                        'rows_parsed' => $rowsParsed,
                        'students_imported' => $studentsImported,
                        'panel_members_imported' => $panelsImported,
                        'errors' => $results['errors'],
                    ],
                ], 422);
            }

            return response()->json([
                'message' => 'Data imported successfully',
                'data' => [
                    'school_year' => $results['school_year']->year,
                    'rows_parsed' => $rowsParsed,
                    'students_imported' => $studentsImported,
                    'panel_members_imported' => $panelsImported,
                    'groups_imported' => $groupsImported,
                    'errors' => $results['errors'],
                ]
            ]);

        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => 'Import failed — preview may have expired',
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cancel import (rollback transaction).
     */
    public function cancelImport(Request $request): JsonResponse
    {
        $request->validate([
            'preview_token' => 'required|string',
        ]);

        try {
            $this->importDataAction->cancel($request->input('preview_token'));
            
            return response()->json([
                'message' => 'Import cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel import',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Import data from CSV/Excel file.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240', // 10MB max
            'school_year_id' => 'required|integer|exists:school_years,id',
            'type' => 'required|string|in:students,panel_members,groups',
        ]);

        try {
            $file = $request->file('file');
            $schoolYearId = $request->input('school_year_id');
            $importType = $request->input('type');

            $results = $this->importDataAction->execute($file, $schoolYearId, $importType);

            $studentsImported = count($results['students']);
            $panelsImported = count($results['panel_members']);
            $groupsImported = count($results['groups']);
            $rowsParsed = $results['rows_parsed'] ?? null;
            $totalImported = $studentsImported + $panelsImported + $groupsImported;

            // If rows were parsed but nothing was imported, treat as failure and give guidance
            if (($rowsParsed !== null && $rowsParsed > 0) && $totalImported === 0) {
                return response()->json([
                    'message' => 'Import completed but no records were imported',
                    'data' => [
                        'school_year' => $results['school_year']->year,
                        'rows_parsed' => $rowsParsed,
                        'students_imported' => $studentsImported,
                        'panel_members_imported' => $panelsImported,
                        'errors' => $results['errors'],
                    ],
                    'hint' => 'Rows were parsed but all rows were skipped (duplicates, validation errors, or empty values). Check the errors array for row-level issues or verify the CSV columns/delimiter.'
                ], 422);
            }

            return response()->json([
                'message' => 'Data imported successfully',
                'data' => [
                    'school_year' => $results['school_year']->year,
                    'rows_parsed' => $rowsParsed,
                    'students_imported' => $studentsImported,
                    'panel_members_imported' => $panelsImported,
                    'groups_imported' => $groupsImported,
                    'errors' => $results['errors'],
                ]
            ]);

        } catch (\RuntimeException $e) {
            // Known parse/validation problems -> surface to user with helpful guidance
            return response()->json([
                'message' => 'Import failed — file appears to contain no data or is malformed',
                'error' => $e->getMessage(),
                'hint' => 'Download the correct template and ensure the CSV uses a comma (,) delimiter and UTF-8 encoding.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Download template for specific type (CSV format with Excel-compatible headers).
     */
    public function downloadTemplate(string $type): StreamedResponse|JsonResponse
    {
        $templates = [
            'students' => [
                'headers' => ['name', 'specialization'],
                'samples' => [
                    ['Juan Dela Cruz', 'Networking'],
                    ['Maria Santos', 'Systems Development'],
                    ['Jose Rizal', 'Networking'],
                ],
                'filename' => 'students_import_template.csv'
            ],
            'panel-members' => [
                'headers' => ['email', 'specialization'],
                'samples' => [
                    ['cristanney.sec@clsu2.edu.ph', 'Networking'],
                    ['anjela.tolentino@clsu2.edu.ph', 'Systems Development'],
                    ['ami.ledesma@clsu2.edu.ph', 'Networking'],
                ],
                'filename' => 'panel_members_import_template.csv'
            ],
            'groups' => [
                'headers' => ['Name of Students per Group', 'Advisee/Chair (clsu2 email)', 'Panel 1 (clsu2 email)', 'Panel 2 (clsu2 email)', 'Capstone Level'],
                'samples' => [
                    ['Monje, Altea Nadine C., Gono, Clarence Aron C., Masing, Jan Bemar', 'cristanney.sec@clsu2.edu.ph', 'anjela.tolentino@clsu2.edu.ph', 'ami.ledesma@clsu2.edu.ph', 'Capstone 1'],
                    ['Villanueva, Florence Faith, Santos, Jc james, Oreña, Wilfred', 'ivanchristian.salinas@clsu2.edu.ph', 'eughenjames.maglaque.2025@clsu2.edu.ph', 'arieljoseph.barza@clsu2.edu.ph', 'Capstone 2'],
                    ['Villa, Jerome, Ramos, Jhans Gabriel L., Lagula, Daryl Kim D.', 'louisewendolyn.hidalgo.2025@clsu2.edu.ph', 'mary.cris.manalang@clsu2.edu.ph', 'genesisbenmark.laguna.2025@clsu2.edu.ph', 'Capstone 1'],
                ],
                'filename' => 'groups_import_template.csv'
            ],
        ];

        if (!isset($templates[$type])) {
            return response()->json([
                'message' => 'Invalid template type',
                'available_types' => array_keys($templates)
            ], 400);
        }

        $template = $templates[$type];

        // Generate CSV file
        $callback = function () use ($template) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fputcsv($file, $template['headers']);
            
            // Write sample data
            foreach ($template['samples'] as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $template['filename'] . '"',
        ]);
    }

    /**
     * Get available template types.
     */
    public function templateTypes(): JsonResponse
    {
        return response()->json([
            'types' => [
                [
                    'key' => 'students',
                    'name' => 'Students Template',
                    'description' => 'Import student records with name and specialization',
                    'columns' => ['name', 'specialization']
                ],
                [
                    'key' => 'panel-members',
                    'name' => 'Panel Members Template',
                    'description' => 'Import professors/panel members with email and specialization',
                    'columns' => ['email', 'specialization']
                ],
                [
                    'key' => 'groups',
                    'name' => 'Groups Template',
                    'description' => 'Import groups with students and panel members',
                    'columns' => ['Name of Students per Group', 'Advisee/Chair (clsu2 email)', 'Panel 1 (clsu2 email)', 'Panel 2 (clsu2 email)', 'Capstone Level']
                ],
            ]
        ]);
    }

    /**
     * Get import template/sample CSV format (legacy).
     */
    public function template(): JsonResponse
    {
        return response()->json([
            'available_imports' => [
                'students' => [
                    'description' => 'Import student records',
                    'columns' => ['name', 'specialization'],
                    'notes' => [
                        'name is required',
                        'specialization is required (Networking or Systems Development)',
                    ]
                ],
                'panel_members' => [
                    'description' => 'Import professor/panel member records',
                    'columns' => ['email', 'specialization'],
                    'notes' => [
                        'email is required and must be valid',
                        'specialization is required (Networking or Systems Development)',
                        'Duplicates by email are skipped',
                        'Roles (Adviser, Chair, Critique) are assigned when creating groups'
                    ]
                ],
                'groups' => [
                    'description' => 'Import groups with students and panel members',
                    'columns' => ['Name of Students per Group', 'Advisee/Chair (clsu2 email)', 'Panel 1 (clsu2 email)', 'Panel 2 (clsu2 email)', 'Capstone Level'],
                    'notes' => [
                        'Name of Students per Group is required (comma-separated names)',
                        'Advisee/Chair (clsu2 email) is required',
                        'Capstone Level should be either Capstone 1 or Capstone 2 (defaults to Capstone 1 if not provided)',
                        'Panel members will be created if they don\'t exist',
                        'Students will be created if they don\'t exist',
                        'Roles: Advisee/Chair is both Adviser and Chair, Panel 1 and Panel 2 are Critique'
                    ]
                ]
            ],
            'download_templates' => [
                'students' => '/api/import/template/students',
                'panel-members' => '/api/import/template/panel-members',
                'groups' => '/api/import/template/groups'
            ]
        ]);
    }
}
