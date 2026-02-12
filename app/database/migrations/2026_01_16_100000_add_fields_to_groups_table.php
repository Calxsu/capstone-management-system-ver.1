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
        Schema::table('groups', function (Blueprint $table) {
            $table->string('name')->nullable()->after('school_year_id');
            $table->string('project_title')->nullable()->after('name');
            $table->text('description')->nullable()->after('project_title');
            $table->unsignedTinyInteger('cap_stage')->default(1)->after('description');
        });

        // Update existing cap_status to cap_stage
        DB::statement("UPDATE groups SET cap_stage = CASE 
            WHEN cap_status = 'CAP1' THEN 1 
            WHEN cap_status = 'CAP2' THEN 2 
            WHEN cap_status = 'CAP3' THEN 3 
            ELSE 1 END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['name', 'project_title', 'description', 'cap_stage']);
        });
    }
};
