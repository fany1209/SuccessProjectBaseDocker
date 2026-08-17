<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cxp_details', function (Blueprint $table) {
            $table->text('comentarios')->nullable();
        });

        Schema::table('cxp_payments', function (Blueprint $table) {
            $table->string('banco')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cxp_details', function (Blueprint $table) {
            $table->dropColumn('comentarios');
        });

        Schema::table('cxp_payments', function (Blueprint $table) {
            $table->dropColumn(['banco', 'metodo_pago', 'user_id']);
        });
    }
};
