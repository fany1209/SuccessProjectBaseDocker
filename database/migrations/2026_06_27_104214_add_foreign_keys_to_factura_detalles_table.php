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
        Schema::table('factura_detalles', function (Blueprint $table) {
            $table->foreign(['factura_id'], 'fk_factura_detalles_factura')->references(['factura_id'])->on('facturas')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura_detalles', function (Blueprint $table) {
            $table->dropForeign('fk_factura_detalles_factura');
        });
    }
};
