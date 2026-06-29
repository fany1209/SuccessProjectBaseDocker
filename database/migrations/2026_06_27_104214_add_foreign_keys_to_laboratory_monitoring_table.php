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
        Schema::table('laboratory_monitoring', function (Blueprint $table) {
            $table->foreign(['productor_id'], 'fk_labseg12_productor')->references(['customer_id'])->on('customers')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratory_monitoring', function (Blueprint $table) {
            $table->dropForeign('fk_labseg12_productor');
        });
    }
};
