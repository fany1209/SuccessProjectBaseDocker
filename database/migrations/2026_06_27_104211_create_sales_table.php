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
            $table->bigIncrements('sale_id');
            $table->string('seller', 150)->nullable();
            $table->boolean('first_time');
            $table->boolean('is_customer');
            $table->string('purchase_order', 150)->nullable()->unique();
            $table->string('invoice', 150)->nullable()->unique();
            $table->string('sale_type', 30);
            $table->string('term', 100)->nullable();
            $table->date('date');
            $table->unsignedBigInteger('folio')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable()->index('sales_customer_id_foreign');
            $table->unsignedBigInteger('prospect_id')->nullable()->index('sales_prospect_id_foreign');
            $table->unsignedBigInteger('user_id')->index('sales_user_id_foreign');
            $table->unsignedBigInteger('sales_status_id')->index('sales_sales_status_id_foreign');
            $table->unsignedBigInteger('sector_id')->index('sector_id');
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
