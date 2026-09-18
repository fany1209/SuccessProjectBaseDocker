<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->integer('order_id');
                $table->string('producto');
                $table->string('cantidad', 100)->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            });

            // Migrar datos existentes de orders hacia order_items para preservar historial
            if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'producto')) {
                $existingOrders = DB::table('orders')->whereNotNull('producto')->where('producto', '!=', '')->get();
                foreach ($existingOrders as $order) {
                    DB::table('order_items')->insert([
                        'order_id'   => $order->id,
                        'producto'   => $order->producto,
                        'cantidad'   => $order->cantidad,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
