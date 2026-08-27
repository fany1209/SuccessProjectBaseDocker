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
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('maintenance_plan_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->enum('status', ['pending', 'printed', 'completed'])->default('pending');
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('evidence_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
