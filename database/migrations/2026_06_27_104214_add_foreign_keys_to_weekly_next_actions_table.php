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
        Schema::table('weekly_next_actions', function (Blueprint $table) {
            $table->foreign(['plan_id'], 'fk_wnext_plan')->references(['id'])->on('weekly_work_plans')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_next_actions', function (Blueprint $table) {
            $table->dropForeign('fk_wnext_plan');
        });
    }
};
