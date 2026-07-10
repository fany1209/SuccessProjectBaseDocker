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
        Schema::create('supplier_directory', function (Blueprint $table) {
            $table->string('Name');
            $table->integer('Code_supplier')->primary();
            $table->string('Product')->nullable();
            $table->text('Address')->nullable();
            $table->string('Phone', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->string('RFC', 15)->nullable();
            $table->string('Contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_directory');
    }
};
