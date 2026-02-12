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
        // First, add 'Chair' to the ENUM
        DB::statement("ALTER TABLE etl_role_values MODIFY COLUMN role ENUM('Adviser', 'Chair Panel', 'Chair', 'Critique') NOT NULL");
        
        // Update 'Chair Panel' to 'Chair' in etl_role_values table
        DB::table('etl_role_values')
            ->where('role', 'Chair Panel')
            ->update([
                'role' => 'Chair',
                'description' => 'Defense Panel Chair'
            ]);
            
        // Remove 'Chair Panel' from the ENUM
        DB::statement("ALTER TABLE etl_role_values MODIFY COLUMN role ENUM('Adviser', 'Chair', 'Critique') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'Chair Panel' back to the ENUM
        DB::statement("ALTER TABLE etl_role_values MODIFY COLUMN role ENUM('Adviser', 'Chair Panel', 'Chair', 'Critique') NOT NULL");
        
        // Revert 'Chair' back to 'Chair Panel'
        DB::table('etl_role_values')
            ->where('role', 'Chair')
            ->update([
                'role' => 'Chair Panel',
                'description' => 'Defense Panel Chair'
            ]);
            
        // Remove 'Chair' from the ENUM
        DB::statement("ALTER TABLE etl_role_values MODIFY COLUMN role ENUM('Adviser', 'Chair Panel', 'Critique') NOT NULL");
    }
};
