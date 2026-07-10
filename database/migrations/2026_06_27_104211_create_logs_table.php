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
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->string('table_name', 40);
            $table->unsignedBigInteger('row_id');
            $table->string('action', 15);
            $table->string('column_name', 50)->nullable();
            $table->string('old_value', 200)->nullable();
            $table->string('new_value', 200)->nullable();
            $table->unsignedBigInteger('app_user_id')->nullable()->index('logs_app_user_id_foreign');
            $table->string('db_user', 100);
            $table->timestamp('date')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
