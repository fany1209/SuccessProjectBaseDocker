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
        Schema::create('pdf_clicks', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('reference_id');
            $table->enum('pdf_type', ['A', 'B', 'C', 'D']);
            $table->bigInteger('user_id')->nullable();
            $table->timestamp('generated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_clicks');
    }
};
