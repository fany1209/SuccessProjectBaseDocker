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
        Schema::create('purchases_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('consecutive',30)->unique()->nullable();
            $table->string('purchase_order',50)->unique()->nullable();
            $table->string('applicant',150);
            $table->string('department',70);
            $table->boolean('data_sheet');
            $table->boolean('safety_sheet');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_requisitions');
    }
};
