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
        Schema::create('tweaks', function (Blueprint $table) {
            $table->bigIncrements('tweak_id');
            $table->string('type', 15);
            $table->double('quantity');
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('inventory_id')->index('tweaks_inventory_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tweaks');
    }
};
