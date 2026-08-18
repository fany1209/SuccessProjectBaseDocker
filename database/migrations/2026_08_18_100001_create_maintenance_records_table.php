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
            $table->unsignedBigInteger('equipment_id');
            $table->date('scheduled_date');
            $table->timestamp('department_confirmed_at')->nullable();
            $table->unsignedBigInteger('department_user_id')->nullable();
            $table->timestamp('admin_confirmed_at')->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->enum('status', ['pending', 'pending_admin', 'completed'])->default('pending');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('equipment_id')->references('id')->on('maintenance_equipments')->onDelete('cascade');
            $table->foreign('department_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('admin_user_id')->references('id')->on('users')->onDelete('set null');
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
