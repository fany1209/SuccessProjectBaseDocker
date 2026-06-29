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
        Schema::create('reagent_inventory', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code', 50);
            $table->string('name');
            $table->decimal('entries', 10)->default(0);
            $table->decimal('exits', 10)->default(0);
            $table->decimal('stock', 10)->default(0);
            $table->string('um', 20)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reagent_inventory');
    }
};
