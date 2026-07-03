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
        DB::statement("ALTER TABLE cxc_details MODIFY COLUMN estatus ENUM('Pendiente', 'Parcial', 'Pagado') DEFAULT 'Pendiente'");
        DB::statement("UPDATE cxc_details SET estatus = 'Pendiente' WHERE estatus NOT IN ('Pendiente', 'Parcial', 'Pagado')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE cxc_details MODIFY COLUMN estatus ENUM('complemento', 'N/A', 'pagado', 'xcobrar') DEFAULT 'xcobrar'");
    }
};
