<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove the 'name' column from panel_members table since we're using email as the primary identifier.
     */
    public function up(): void
    {
        $connection = Schema::getConnection()->getDriverName();

        if ($connection === 'mysql') {
            // Drop the name column
            try {
                DB::statement('ALTER TABLE `panel_members` DROP COLUMN `name`');
            } catch (\Throwable $e) {
                // Column may not exist; ignore
                info('Could not drop panel_members.name: ' . $e->getMessage());
            }

            // Drop the role column if it exists (role is now determined per group via pivot table)
            try {
                DB::statement('ALTER TABLE `panel_members` DROP COLUMN `role`');
            } catch (\Throwable $e) {
                // Column may not exist; ignore
                info('Could not drop panel_members.role: ' . $e->getMessage());
            }

            // Make email NOT NULL and unique
            try {
                DB::statement('ALTER TABLE `panel_members` MODIFY `email` VARCHAR(255) NOT NULL');
                DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS panel_members_email_unique ON panel_members (email)');
            } catch (\Throwable $e) {
                info('Could not modify panel_members.email: ' . $e->getMessage());
            }
        }

        // For SQLite (tests), just proceed - the column will be handled by the main migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Schema::getConnection()->getDriverName();

        if ($connection === 'mysql') {
            // Add back the name column
            try {
                DB::statement('ALTER TABLE `panel_members` ADD COLUMN `name` VARCHAR(255) AFTER `id`');
            } catch (\Throwable $e) {
                info('Could not add panel_members.name: ' . $e->getMessage());
            }

            // Add back the role column
            try {
                DB::statement("ALTER TABLE `panel_members` ADD COLUMN `role` ENUM('Adviser', 'Chair Panel', 'Critique') AFTER `status`");
            } catch (\Throwable $e) {
                info('Could not add panel_members.role: ' . $e->getMessage());
            }
        }
    }
};
