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
        Schema::table('cli', function (Blueprint $table) {
            $table->foreign(['concept_id'])->references(['concept_id'])->on('concepts')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['inventory_id'])->references(['inventory_id'])->on('inventory')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['location_id'])->references(['location_id'])->on('locations')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cli', function (Blueprint $table) {
            $table->dropForeign('cli_concept_id_foreign');
            $table->dropForeign('cli_inventory_id_foreign');
            $table->dropForeign('cli_location_id_foreign');
        });
    }
};
