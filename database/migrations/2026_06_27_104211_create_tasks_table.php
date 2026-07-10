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
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('admin_id')->index('idx_tasks_admin');
            $table->unsignedBigInteger('user_id')->index('idx_tasks_user');
            $table->enum('status', ['pending', 'in_progress', 'review', 'completed'])->default('pending')->index('idx_tasks_status');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->dateTime('due_date')->nullable()->comment('Fecha límite');
            $table->dateTime('completed_at')->nullable()->comment('Fecha en que se terminó');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
