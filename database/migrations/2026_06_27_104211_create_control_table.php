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
        Schema::create('control', function (Blueprint $table) {
            $table->bigIncrements('control_id');
            $table->double('temperature');
            $table->double('humidity');
            $table->string('host_ip', 15);
            $table->string('host_user', 50);
            $table->string('host_name', 60);
            $table->unsignedBigInteger('warehouse_id')->index('control_warehouse_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('control_user_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control');
    }
};
