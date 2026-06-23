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
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sale_id');
            $table->string('seller', 150)->nullable(); //QUitar el nullable
            $table->boolean('first_time');
            $table->boolean('is_customer');
            $table->string('purchase_order', 150)->unique()->nullable();
            $table->string('invoice', 150)->unique()->nullable();
            $table->string('sale_type', 30);
            $table->string('term', 100)->nullable();
            $table->date('date');
            $table->unsignedBigInteger('folio');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('prospect_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sales_status_id');
            $table->unsignedBigInteger('sector_id');//COnsiderar
            $table->foreign('customer_id')->references('customer_id')->on('customers');
            $table->foreign('prospect_id')->references('prospect_id')->on('prospects');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('sales_status_id')->references('sales_status_id')->on('sales_status');
            $table->foreign('sector_id')->references('sector_id')->on('sectors');//COnsiderar
            $table->unique(['folio','sector_id']);//COnsiderar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
