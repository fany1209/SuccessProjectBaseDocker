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
        Schema::create('weekly_work_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('week_range', 100)->nullable();
            $table->date('review_date')->nullable()->index('idx_review_date');
            $table->string('project_name')->nullable()->index('idx_project');
            $table->string('responsible_name')->nullable()->index('idx_responsible');
            $table->integer('total_hours')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_work_plans');
    }
};
