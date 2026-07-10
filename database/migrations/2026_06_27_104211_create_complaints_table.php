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
        Schema::create('complaints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha')->index('idx_complaints_fecha');
            $table->enum('tipo', ['peticion', 'queja', 'reclamo', 'sugerencia', 'felicitacion', 'denuncia'])->index('idx_complaints_tipo');
            $table->json('motivos')->nullable();
            $table->string('motivo_otro')->nullable();
            $table->text('descripcion');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
