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
        Schema::table('outputs', function (Blueprint $table) {
            $table->foreign(['customer_id'])->references(['customer_id'])->on('customers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['transport_line_id'])->references(['transport_line_id'])->on('transport_lines')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outputs', function (Blueprint $table) {
            $table->dropForeign('outputs_customer_id_foreign');
            $table->dropForeign('outputs_transport_line_id_foreign');
        });
    }
};
