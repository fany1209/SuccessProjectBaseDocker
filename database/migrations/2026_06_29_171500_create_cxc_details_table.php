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
        Schema::create('cxc_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id')->unique();
            $table->enum('metodo_pago', ['N/A', 'PUE', 'PPD'])->default('N/A');
            $table->text('descripcion')->nullable();
            $table->enum('estatus', ['complemento', 'N/A', 'pagado', 'xcobrar'])->default('xcobrar');
            $table->date('fecha_conclusion')->nullable();
            $table->boolean('is_canceled')->default(false);
            $table->timestamps();

            $table->foreign('sale_id')->references('sale_id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cxc_details');
    }
};
