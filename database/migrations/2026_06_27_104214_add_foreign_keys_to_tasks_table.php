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
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign(['admin_id'], 'fk_tasks_admin_id')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['user_id'], 'fk_tasks_user_id')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('fk_tasks_admin_id');
            $table->dropForeign('fk_tasks_user_id');
        });
    }
};
