<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Check if course column exists and rename it
            if (Schema::hasColumn('students', 'course')) {
                $table->renameColumn('course', 'specialization');
            } else {
                // If course doesn't exist, just add specialization
                $table->string('specialization')->nullable()->after('student_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'specialization')) {
                $table->renameColumn('specialization', 'course');
            }
        });
    }
};
