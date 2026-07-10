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
        Schema::create('lot_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('department', 100);
            $table->dateTime('requested_at')->nullable()->useCurrent();
            $table->enum('status', ['pendiente', 'terminado'])->nullable()->default('pendiente');
            $table->text('comments')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_requests');
    }
};
