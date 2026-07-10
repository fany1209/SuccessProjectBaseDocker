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
        Schema::table('tweaks', function (Blueprint $table) {
            $table->foreign(['inventory_id'])->references(['inventory_id'])->on('inventory')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tweaks', function (Blueprint $table) {
            $table->dropForeign('tweaks_inventory_id_foreign');
        });
    }
};
