<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First update existing 'Chair Panel' values to 'Chair'
        DB::statement("ALTER TABLE group_panels MODIFY COLUMN role ENUM('Adviser', 'Chair Panel', 'Chair', 'Critique') NOT NULL");
        DB::table('group_panels')->where('role', 'Chair Panel')->update(['role' => 'Chair']);
        // Then remove 'Chair Panel' from the enum
        DB::statement("ALTER TABLE group_panels MODIFY COLUMN role ENUM('Adviser', 'Chair', 'Critique') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: add 'Chair Panel' back and convert 'Chair' to 'Chair Panel'
        DB::statement("ALTER TABLE group_panels MODIFY COLUMN role ENUM('Adviser', 'Chair Panel', 'Chair', 'Critique') NOT NULL");
        DB::table('group_panels')->where('role', 'Chair')->update(['role' => 'Chair Panel']);
        DB::statement("ALTER TABLE group_panels MODIFY COLUMN role ENUM('Adviser', 'Chair Panel', 'Critique') NOT NULL");
    }
};
