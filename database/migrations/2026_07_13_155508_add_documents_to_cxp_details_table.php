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
        Schema::table('cxp_details', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('is_canceled');
            $table->string('xml_path')->nullable()->after('pdf_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cxp_details', function (Blueprint $table) {
            $table->dropColumn(['pdf_path', 'xml_path']);
        });
    }
};
