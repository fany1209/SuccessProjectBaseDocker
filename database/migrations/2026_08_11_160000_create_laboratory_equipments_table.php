<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laboratory_equipments', function (Blueprint $table) {
            $table->id();
            $table->string('internal_code')->nullable()->comment('Código interno');
            $table->string('name')->comment('Nombre del equipo');
            $table->decimal('quantity', 10, 2)->default(0)->comment('Cantidad');
            $table->string('brand')->nullable()->comment('Marca');
            $table->enum('status', ['funcional', 'en reparacion', 'no funciona'])->default('funcional')->comment('Estado de equipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laboratory_equipments');
    }
};
