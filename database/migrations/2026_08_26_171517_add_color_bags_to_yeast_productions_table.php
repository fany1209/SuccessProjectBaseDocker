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
        Schema::table('yeast_productions', function (Blueprint $table) {
            $table->dropColumn('color');
            $table->integer('bags_natural')->nullable()->after('external_weight');
            $table->integer('bags_mix')->nullable()->after('bags_natural');
            $table->integer('bags_white')->nullable()->after('bags_mix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yeast_productions', function (Blueprint $table) {
            $table->string('color', 100)->nullable()->after('external_weight');
            $table->dropColumn(['bags_natural', 'bags_mix', 'bags_white']);
        });
    }
};
