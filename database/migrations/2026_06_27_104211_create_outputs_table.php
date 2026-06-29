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
        Schema::create('outputs', function (Blueprint $table) {
            $table->bigIncrements('output_id');
            $table->string('operator', 200)->nullable();
            $table->string('vendedor', 200)->nullable();
            $table->string('license_number', 20)->nullable();
            $table->boolean('security_seal');
            $table->string('security_seal_number', 50)->nullable();
            $table->string('unit_plates', 20)->nullable();
            $table->string('trailer_plates', 20)->nullable();
            $table->string('comments', 300)->nullable();
            $table->unsignedBigInteger('customer_id')->index('outputs_customer_id_foreign');
            $table->unsignedBigInteger('transport_line_id')->nullable()->index('outputs_transport_line_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outputs');
    }
};
