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
        Schema::create('quotes', function (Blueprint $table) {
            $table->bigIncrements('quote_id');
            $table->string('folio', 30)->unique();
            $table->date('date')->nullable();
            $table->string('company');
            $table->string('attention');
            $table->string('department');
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('place_of_delivery')->nullable();
            $table->string('transport_specification')->nullable();
            $table->date('deadline')->nullable();
            $table->string('terms')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('quotes_status_id')->index('quotes_quotes_status_id_foreign');
            $table->unsignedBigInteger('user_id')->index('quotes_user_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
