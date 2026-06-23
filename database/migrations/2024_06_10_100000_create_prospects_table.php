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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id('prospect_id');
            $table->string('name', 200);
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('rfc', 13)->nullable();
            $table->string('district', 80)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('address', 200)->nullable();
            $table->unsignedBigInteger('sector_id');
            $table->foreign('sector_id')->references('sector_id')->on('sectors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
