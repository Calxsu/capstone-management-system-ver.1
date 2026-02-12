<?php

namespace App\Console\Commands;

use App\Actions\ImportDataAction;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class DebugImportFileCommand extends Command
{
    protected $signature = 'debug:import-file {path} {schoolYearId=1} {type=panel_members} {--dry}';
    protected $description = 'Debug import for a CSV file using ImportDataAction (prints parsed rows, created items and errors)';

    public function handle(ImportDataAction $import)
    {
        $path = $this->argument('path');
        $schoolYearId = (int) $this->argument('schoolYearId');
        $type = $this->argument('type');
        $dry = $this->option('dry');

        $fs = new Filesystem();
        if (! $fs->exists($path)) {
            $this->error("File not found: {$path}");
            return 2;
        }

        // Create a Symfony UploadedFile equivalent
        $uploaded = new \Illuminate\Http\UploadedFile($path, basename($path), null, null, true);

        try {
            if ($dry) {
                $this->info('Running in dry mode — parsing only');
                $data = (new \App\Actions\ImportDataAction(app(\App\Repositories\SchoolYearRepositoryInterface::class), app(\App\Repositories\StudentRepositoryInterface::class), app(\App\Repositories\PanelMemberRepositoryInterface::class)))->parseCsvFile($uploaded);
                $this->line('Rows parsed: ' . count($data));
                $this->line('First 5 rows:');
                foreach (array_slice($data, 0, 5) as $i => $row) {
                    $this->line(($i+1) . ': ' . json_encode($row));
                }
                return 0;
            }

            $results = $import->execute($uploaded, $schoolYearId, $type);

            $this->info('Import finished');
            $this->line('Rows parsed: ' . ($results['rows_parsed'] ?? 'n/a'));
            $this->line('Students imported: ' . count($results['students']));
            $this->line('Panel members imported: ' . count($results['panel_members']));
            $this->line('Errors: ' . count($results['errors']));

            if (! empty($results['errors'])) {
                $this->line('--- row-level errors ---');
                foreach ($results['errors'] as $err) {
                    $this->line($err);
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            return 1;
        }
    }
}
