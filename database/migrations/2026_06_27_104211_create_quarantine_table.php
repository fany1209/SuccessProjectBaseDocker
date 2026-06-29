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
        Schema::create('quarantine', function (Blueprint $table) {
            $table->bigIncrements('quarantine_id');
            $table->double('quantity');
            $table->string('notes', 350);
            $table->unsignedBigInteger('inventory_id')->index('quarantine_inventory_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarantine');
    }
};
