<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::getConnection()->getDriverName();

        // MySQL: drop unique index and make column nullable
        if ($connection === 'mysql') {
            try {
                DB::statement('ALTER TABLE `panel_members` DROP INDEX `panel_members_email_unique`');
            } catch (\Throwable $e) {
                // index may not exist; ignore
            }

            try {
                DB::statement('ALTER TABLE `panel_members` MODIFY `email` VARCHAR(255) NULL');
            } catch (\Throwable $e) {
                // best-effort; if it fails, surface in logs but do not break migration run
                info('Could not modify panel_members.email to nullable: ' . $e->getMessage());
            }

            return;
        }

        // SQLite (tests) — altering columns is limited. If running against sqlite, do nothing here
        // Tests will continue to work because importer falls back to generating placeholder emails when schema requires it.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::getConnection()->getDriverName();

        if ($connection === 'mysql') {
            try {
                DB::statement('ALTER TABLE `panel_members` MODIFY `email` VARCHAR(255) NOT NULL');
                DB::statement('CREATE UNIQUE INDEX panel_members_email_unique ON panel_members (email)');
            } catch (\Throwable $e) {
                info('Could not revert panel_members.email migration: ' . $e->getMessage());
            }
        }
    }
};
