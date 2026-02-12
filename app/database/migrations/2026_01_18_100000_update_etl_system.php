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
        // Remove role from panel_members - professors can have any role per group
        Schema::table('panel_members', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Add semester to school_years for ETL computation per semester
        Schema::table('school_years', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->default(1)->after('year');
        });

        // Add defense_status to groups for tracking defended/retained projects
        Schema::table('groups', function (Blueprint $table) {
            $table->enum('defense_status', ['pending', 'defended', 'retained'])->default('pending')->after('cap_status');
            $table->date('defense_date')->nullable()->after('defense_status');
        });

        // Add ETL role values table for configurable ETL values
        Schema::create('etl_role_values', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['Adviser', 'Chair Panel', 'Critique']);
            $table->decimal('etl_value', 5, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default ETL values
        DB::table('etl_role_values')->insert([
            ['role' => 'Adviser', 'etl_value' => 3.00, 'description' => 'Thesis/Capstone Adviser', 'created_at' => now(), 'updated_at' => now()],
            ['role' => 'Chair Panel', 'etl_value' => 2.00, 'description' => 'Defense Panel Chair', 'created_at' => now(), 'updated_at' => now()],
            ['role' => 'Critique', 'etl_value' => 1.50, 'description' => 'Panel Critique/Member', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etl_role_values');

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['defense_status', 'defense_date']);
        });

        Schema::table('school_years', function (Blueprint $table) {
            $table->dropColumn('semester');
        });

        Schema::table('panel_members', function (Blueprint $table) {
            $table->enum('role', ['Adviser', 'Chair Panel', 'Critique'])->default('Adviser');
        });
    }
};
