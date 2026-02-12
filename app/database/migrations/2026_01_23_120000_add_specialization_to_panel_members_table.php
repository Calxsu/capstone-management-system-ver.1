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
        Schema::table('panel_members', function (Blueprint $table) {
            if (! Schema::hasColumn('panel_members', 'specialization')) {
                $table->string('specialization')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panel_members', function (Blueprint $table) {
            if (Schema::hasColumn('panel_members', 'specialization')) {
                $table->dropColumn('specialization');
            }
        });
    }
};
