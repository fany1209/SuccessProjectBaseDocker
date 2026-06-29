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
        Schema::create('cli', function (Blueprint $table) {
            $table->bigIncrements('cli_id');
            $table->unsignedBigInteger('inventory_id')->index('cli_inventory_id_foreign');
            $table->unsignedBigInteger('location_id')->index('cli_location_id_foreign');
            $table->unsignedBigInteger('concept_id')->index('cli_concept_id_foreign');
            $table->string('bag_number', 20)->nullable()->unique();
            $table->decimal('quantity', 12, 3);
            $table->decimal('weight_per_unit', 10, 3);
            $table->decimal('net_weight', 12, 3);
            $table->json('sq_certificate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cli');
    }
};
