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
        Schema::create('requisition_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 300);
            $table->string('supplier', 100);
            $table->enum('insumo', ['directo', 'indirecto'])->default('directo');
            $table->string('url', 1024);
            $table->string('use', 100);
            $table->string('quantity', 30);
            $table->string('image_url', 2048)->nullable();
            $table->unsignedBigInteger('pr_id')->index('requisition_products_pr_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_products');
    }
};
