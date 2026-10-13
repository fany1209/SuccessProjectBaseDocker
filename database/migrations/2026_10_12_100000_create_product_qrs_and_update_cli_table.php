<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Añadir columnas uuid y detailed_description a cli si no existen
        Schema::table('cli', function (Blueprint $table) {
            if (!Schema::hasColumn('cli', 'uuid')) {
                $table->char('uuid', 36)->nullable()->unique()->after('cli_id');
            }
            if (!Schema::hasColumn('cli', 'detailed_description')) {
                $table->text('detailed_description')->nullable()->after('sq_certificate');
            }
        });

        // 2. Crear tabla product_qrs
        if (!Schema::hasTable('product_qrs')) {
            Schema::create('product_qrs', function (Blueprint $table) {
                $table->id();
                $table->char('uuid', 36)->unique()->index();
                $table->unsignedBigInteger('cli_id')->nullable()->index();
                $table->unsignedBigInteger('inventory_id')->nullable()->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('location_id')->nullable()->index();
                $table->unsignedBigInteger('concept_id')->nullable()->index();
                $table->string('bag_number')->nullable()->index();
                $table->string('batch')->nullable()->index();
                $table->decimal('quantity', 12, 4)->nullable();
                $table->decimal('weight_per_unit', 12, 4)->nullable();
                $table->decimal('net_weight', 12, 4)->nullable();
                $table->string('unit', 50)->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('cli_id')->references('cli_id')->on('cli')->onDelete('set null');
            });
        }

        // 3. Registrar permiso product_inf.show y asignar a roles Admin y Warehouse
        try {
            $permission = Permission::firstOrCreate(
                ['name' => 'product_inf.show', 'guard_name' => 'web'],
                ['description' => 'Acceso y visualización al módulo Product Inf']
            );
            $adminRole = Role::where('name', 'Admin')->first();
            if ($adminRole && !$adminRole->hasPermissionTo($permission)) {
                $adminRole->givePermissionTo($permission);
            }
            $warehouseRole = Role::where('name', 'Warehouse')->first();
            if ($warehouseRole && !$warehouseRole->hasPermissionTo($permission)) {
                $warehouseRole->givePermissionTo($permission);
            }
        } catch (\Throwable $e) {
            // Continuar si la tabla de permisos no está lista
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_qrs');

        Schema::table('cli', function (Blueprint $table) {
            if (Schema::hasColumn('cli', 'detailed_description')) {
                $table->dropColumn('detailed_description');
            }
            if (Schema::hasColumn('cli', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });

        try {
            $permission = Permission::where('name', 'product_inf.show')->first();
            if ($permission) {
                $permission->delete();
            }
        } catch (\Throwable $e) {
        }
    }
};
