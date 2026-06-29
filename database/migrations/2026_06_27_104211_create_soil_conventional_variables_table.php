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
        Schema::create('soil_conventional_variables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('soil_analysis_id')->index('idx_soil_analysis_id');
            $table->string('variable_name')->nullable();
            $table->string('result_text')->nullable();
            $table->string('unit_text', 50)->nullable();
            $table->unsignedSmallInteger('position_order')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_conventional_variables');
    }
};
