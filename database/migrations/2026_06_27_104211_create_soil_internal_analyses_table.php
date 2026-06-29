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
        Schema::create('soil_internal_analyses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('report_code', 100)->nullable()->index('idx_report_code');
            $table->date('entry_date')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('client_name')->nullable()->index('idx_client_name');
            $table->string('client_city')->nullable();
            $table->string('client_address', 500)->nullable();
            $table->string('client_phone', 50)->nullable();
            $table->string('crop_type')->nullable();
            $table->string('system_type')->nullable();
            $table->string('plant_type')->nullable();
            $table->string('sample_weight', 50)->nullable();
            $table->boolean('is_control')->default(false);
            $table->string('location')->nullable();
            $table->string('sampling_type')->nullable();
            $table->string('sampling_responsible')->nullable();
            $table->string('purpose', 1000)->nullable();
            $table->decimal('n_value_mgkg', 10)->nullable();
            $table->decimal('p_value_mgkg', 10)->nullable();
            $table->decimal('k_value_mgkg', 10)->nullable();
            $table->decimal('sand_pct', 5)->nullable();
            $table->decimal('silt_pct', 5)->nullable();
            $table->decimal('clay_pct', 5)->nullable();
            $table->string('classification')->nullable();
            $table->string('triangle_src', 1000)->nullable();
            $table->longText('image_path')->nullable();
            $table->string('image_caption')->nullable();
            $table->string('image_mime', 50)->nullable();
            $table->integer('image_size_kb')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['entry_date', 'issue_date'], 'idx_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_internal_analyses');
    }
};
