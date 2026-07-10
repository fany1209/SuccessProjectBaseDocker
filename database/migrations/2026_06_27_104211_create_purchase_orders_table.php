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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->unsignedBigInteger('supplier_id')->index('fk_purchase_orders_supplier');
            $table->string('contact')->nullable();
            $table->string('delivery_time', 150)->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('guia')->nullable();
            $table->string('cfdi', 10)->nullable();
            $table->string('payment_method', 10)->nullable();
            $table->string('method_payment', 10)->nullable();
            $table->date('application_date')->nullable();
            $table->string('applicant')->nullable();
            $table->decimal('price', 10)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
