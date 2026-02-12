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
        // Change semester from tinyint to string to support descriptive values
        Schema::table('school_years', function (Blueprint $table) {
            $table->string('semester', 50)->default('1st Semester')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_years', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->default(1)->change();
        });
    }
};
