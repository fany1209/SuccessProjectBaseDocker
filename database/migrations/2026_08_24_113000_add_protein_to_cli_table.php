<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cli', function (Blueprint $table) {
            $table->decimal('protein', 8, 2)->nullable()->after('bag_number');
        });
    }

    public function down(): void
    {
        Schema::table('cli', function (Blueprint $table) {
            $table->dropColumn('protein');
        });
    }
};
