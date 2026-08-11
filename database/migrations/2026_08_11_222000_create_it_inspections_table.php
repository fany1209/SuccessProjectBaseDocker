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
        Schema::create('it_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->date('date');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();
            $table->string('area')->nullable();
            
            // Checklists
            $table->string('req1')->default('cumple');
            $table->string('req2')->default('cumple');
            $table->string('req3')->default('cumple');
            $table->string('req4')->default('cumple');
            $table->string('req5')->default('cumple');
            
            $table->text('observations')->nullable();
            $table->string('inspector_name')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_inspections');
    }
};
