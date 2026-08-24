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
        Schema::table('lot_requests', function (Blueprint $table) {
            $table->string('product')->nullable()->after('comments');
            $table->string('quantity')->nullable()->after('product');
            $table->string('provider')->nullable()->after('quantity');
            $table->string('collector')->nullable()->after('provider');
            $table->string('sector')->nullable()->after('collector');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lot_requests', function (Blueprint $table) {
            $table->dropColumn(['product', 'quantity', 'provider', 'collector', 'sector']);
        });
    }
};
