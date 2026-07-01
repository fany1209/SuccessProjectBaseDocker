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
        Schema::create('cxc_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cxc_detail_id');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->timestamps();

            $table->foreign('cxc_detail_id')->references('id')->on('cxc_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cxc_payments');
    }
};
