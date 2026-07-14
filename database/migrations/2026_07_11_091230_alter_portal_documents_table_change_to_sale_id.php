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
        Schema::table('portal_documents', function (Blueprint $table) {
            $table->dropForeign(['portal_user_id']);
            $table->dropColumn('portal_user_id');

            $table->unsignedBigInteger('sale_id')->after('id');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal_documents', function (Blueprint $table) {
            $table->dropColumn('sale_id');
            
            $table->unsignedBigInteger('portal_user_id')->after('id');
            $table->foreign('portal_user_id')->references('id')->on('portal_users')->onDelete('cascade');
        });
    }
};
