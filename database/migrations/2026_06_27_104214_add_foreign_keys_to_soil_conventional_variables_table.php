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
        Schema::table('soil_conventional_variables', function (Blueprint $table) {
            $table->foreign(['soil_analysis_id'], 'fk_scvars_soil')->references(['id'])->on('soil_internal_analyses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soil_conventional_variables', function (Blueprint $table) {
            $table->dropForeign('fk_scvars_soil');
        });
    }
};
