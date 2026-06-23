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
            $table->id('control_id');
            $table->double('temperature', 5, 2);
            $table->double('humidity', 5, 2);
            $table->string('host_ip', 15);
            $table->string('host_user', 50);
            $table->string('host_name', 60);
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('warehouse_id')->references('warehouse_id')->on('warehouses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
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
