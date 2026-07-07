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
        Schema::create('cxp_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cxp_detail_id');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('comprobante')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('cxp_detail_id')->references('id')->on('cxp_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cxp_payments');
    }
};
