<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes the unused start_date and end_date columns
     * from the school_years table.
     */
    public function up(): void
    {
        Schema::table('school_years', function (Blueprint $table) {
            $table->dropColumnIfExists('start_date');
            $table->dropColumnIfExists('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_years', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }
};
