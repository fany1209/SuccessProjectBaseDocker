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
        Schema::create('production_material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('area', 50)->comment('fertil, vitayela, etc.');
            $table->string('applicant_name', 100);
            $table->string('status', 50)->default('Pendiente')->comment('Pendiente, Surtido, Cancelado');
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        Schema::create('production_material_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('product_name');
            $table->decimal('quantity', 10, 3)->default(0);
            $table->decimal('dispatched_quantity', 10, 3)->default(0);
            $table->timestamps();

            $table->foreign('request_id')
                  ->references('id')->on('production_material_requests')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_material_request_items');
        Schema::dropIfExists('production_material_requests');
    }
};
