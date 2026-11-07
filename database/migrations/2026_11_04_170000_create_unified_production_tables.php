<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('production_records')) {
            Schema::create('production_records', function (Blueprint $table) {
                $table->id('production_record_id');
                $table->string('area', 50)->index()->comment('fertil, vitayela');
                $table->date('fecha_preparacion')->nullable();
                $table->decimal('kg_preparados', 10, 2)->nullable();
                $table->date('fecha_ensacado')->nullable();
                $table->decimal('kg_ensacados', 10, 2)->nullable();
                $table->integer('num_sacos')->nullable();
                $table->text('descripcion')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('production_inventories')) {
            Schema::create('production_inventories', function (Blueprint $table) {
                $table->id('production_inventory_id');
                $table->string('area', 50)->index()->comment('fertil, vitayela');
                $table->string('producto_descripcion')->nullable();
                $table->decimal('cantidad', 10, 2)->default(0);
                $table->string('unidad', 50)->nullable();
                $table->decimal('stock_min', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('production_inventory_movements')) {
            Schema::create('production_inventory_movements', function (Blueprint $table) {
                $table->id('movement_id');
                $table->unsignedBigInteger('production_inventory_id');
                $table->string('tipo', 50)->comment('Entrada, Salida, Ajuste');
                $table->decimal('cantidad', 10, 2);
                $table->timestamps();

                $table->foreign('production_inventory_id', 'fk_prod_inv_mov_id')
                    ->references('production_inventory_id')
                    ->on('production_inventories')
                    ->onDelete('cascade');
            });
        }

        // Migrar registros existentes de Fertil y Vitayela si las tablas existen y la nueva está vacía
        if (DB::table('production_records')->count() === 0) {
            if (Schema::hasTable('fertil_productions')) {
                $fertilProductions = DB::table('fertil_productions')->orderBy('fertil_production_id', 'asc')->get();
                foreach ($fertilProductions as $fp) {
                    DB::table('production_records')->insert([
                        'area'              => 'fertil',
                        'fecha_preparacion' => $fp->fecha_preparacion,
                        'kg_preparados'     => $fp->kg_preparados,
                        'fecha_ensacado'    => $fp->fecha_ensacado,
                        'kg_ensacados'      => $fp->kg_ensacados,
                        'num_sacos'         => $fp->num_sacos,
                        'descripcion'       => $fp->descripcion,
                        'created_at'        => $fp->created_at ?? now(),
                        'updated_at'        => $fp->updated_at ?? now(),
                    ]);
                }
            }

            if (Schema::hasTable('vitayela_productions')) {
                $vitayelaProductions = DB::table('vitayela_productions')->orderBy('vitayela_production_id', 'asc')->get();
                foreach ($vitayelaProductions as $vp) {
                    DB::table('production_records')->insert([
                        'area'              => 'vitayela',
                        'fecha_preparacion' => $vp->fecha_preparacion,
                        'kg_preparados'     => $vp->kg_preparados,
                        'fecha_ensacado'    => $vp->fecha_ensacado,
                        'kg_ensacados'      => $vp->kg_ensacados,
                        'num_sacos'         => $vp->num_sacos,
                        'descripcion'       => $vp->descripcion,
                        'created_at'        => $vp->created_at ?? now(),
                        'updated_at'        => $vp->updated_at ?? now(),
                    ]);
                }
            }
        }

        // Migrar inventarios y movimientos existentes de Fertil y Vitayela
        if (DB::table('production_inventories')->count() === 0) {
            if (Schema::hasTable('fertil_inventories')) {
                $fertilInventories = DB::table('fertil_inventories')->orderBy('fertil_inventory_id', 'asc')->get();
                foreach ($fertilInventories as $fi) {
                    $newId = DB::table('production_inventories')->insertGetId([
                        'area'                 => 'fertil',
                        'producto_descripcion' => $fi->producto_descripcion,
                        'cantidad'             => $fi->cantidad ?? 0,
                        'unidad'               => $fi->unidad,
                        'stock_min'            => $fi->stock_min ?? 0,
                        'created_at'           => $fi->created_at ?? now(),
                        'updated_at'           => $fi->updated_at ?? now(),
                    ], 'production_inventory_id');

                    if (Schema::hasTable('fertil_inventory_movements')) {
                        $movements = DB::table('fertil_inventory_movements')
                            ->where('fertil_inventory_id', $fi->fertil_inventory_id)
                            ->orderBy('movement_id', 'asc')
                            ->get();

                        foreach ($movements as $mov) {
                            DB::table('production_inventory_movements')->insert([
                                'production_inventory_id' => $newId,
                                'tipo'                    => $mov->tipo,
                                'cantidad'                => $mov->cantidad,
                                'created_at'              => $mov->created_at ?? now(),
                                'updated_at'              => $mov->updated_at ?? now(),
                            ]);
                        }
                    }
                }
            }

            if (Schema::hasTable('vitayela_inventories')) {
                $vitayelaInventories = DB::table('vitayela_inventories')->orderBy('vitayela_inventory_id', 'asc')->get();
                foreach ($vitayelaInventories as $vi) {
                    $newId = DB::table('production_inventories')->insertGetId([
                        'area'                 => 'vitayela',
                        'producto_descripcion' => $vi->producto_descripcion,
                        'cantidad'             => $vi->cantidad ?? 0,
                        'unidad'               => $vi->unidad,
                        'stock_min'            => $vi->stock_min ?? 0,
                        'created_at'           => $vi->created_at ?? now(),
                        'updated_at'           => $vi->updated_at ?? now(),
                    ], 'production_inventory_id');

                    if (Schema::hasTable('vitayela_inventory_movements')) {
                        $movements = DB::table('vitayela_inventory_movements')
                            ->where('vitayela_inventory_id', $vi->vitayela_inventory_id)
                            ->orderBy('movement_id', 'asc')
                            ->get();

                        foreach ($movements as $mov) {
                            DB::table('production_inventory_movements')->insert([
                                'production_inventory_id' => $newId,
                                'tipo'                    => $mov->tipo,
                                'cantidad'                => $mov->cantidad,
                                'created_at'              => $mov->created_at ?? now(),
                                'updated_at'              => $mov->updated_at ?? now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_inventory_movements');
        Schema::dropIfExists('production_inventories');
        Schema::dropIfExists('production_records');
    }
};

