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
        Schema::create('inputs', function (Blueprint $table) {
            $table->bigIncrements('input_id');
            $table->string('operator', 200)->nullable();
            $table->string('license_number', 20)->nullable();
            $table->boolean('security_seal');
            $table->string('security_seal_number', 50)->nullable();
            $table->string('unit_plates', 20)->nullable();
            $table->string('trailer_plates', 20)->nullable();
            $table->string('comments', 300)->nullable();
            $table->unsignedBigInteger('supplier_id')->index('inputs_supplier_id_foreign');
            $table->unsignedBigInteger('transport_line_id')->nullable()->index('inputs_transport_line_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inputs');
    }
};
