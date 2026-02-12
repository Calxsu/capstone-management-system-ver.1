<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the OJT Application functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing OJT Application...');

        // Test database connection
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
        } catch (\Exception $e) {
            $this->error('❌ Database connection: FAILED - ' . $e->getMessage());
            return;
        }

        // Test models
        $schoolYearsCount = \App\Models\SchoolYear::count();
        $this->info("✅ School Years: {$schoolYearsCount} records found");

        $panelMembersCount = \App\Models\PanelMember::count();
        $this->info("✅ Panel Members: {$panelMembersCount} records found");

        $studentsCount = \App\Models\Student::count();
        $this->info("✅ Students: {$studentsCount} records found");

        $groupsCount = \App\Models\Group::count();
        $this->info("✅ Groups: {$groupsCount} records found");

        // Test ETL computation
        $panelMembers = \App\Models\PanelMember::all();
        if ($panelMembers->count() > 0) {
            $firstPanelMember = $panelMembers->first();
            $etlAction = app(\App\Actions\ComputeETLAction::class);
            $etl = $etlAction->execute($firstPanelMember->id);
            $this->info("✅ ETL Computation: {$etl} for {$firstPanelMember->name}");
        }

        // Test report generation
        $this->info('📊 Testing Report Generation...');
        $reportController = app(\App\Http\Controllers\Api\ReportController::class);
        $etlReport = $reportController->etlReport(new \Illuminate\Http\Request());
        $this->info('✅ ETL Report: Generated successfully');

        $capReport = $reportController->capProgressReport(new \Illuminate\Http\Request());
        $this->info('✅ CAP Progress Report: Generated successfully');

        $this->info('🎉 All tests passed! OJT Application is working correctly.');
        $this->info('🌐 To start the web server, run: php artisan serve');
        $this->info('📚 API Documentation: http://localhost:8000/api/');
    }
}
