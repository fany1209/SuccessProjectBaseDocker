<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('portal_users', function (Blueprint $table) {
    $table->id();
    $table->string('nombre_contacto');
    $table->string('empresa')->nullable();
    $table->string('email')->unique();
    $table->string('password');
    $table->boolean('is_active')->default(true);
    
    // Aquí vinculamos, nota que pasamos 'customer_id' como segundo argumento 
    // porque es el nombre de la PK en tu tabla customers
    $table->unsignedBigInteger('customer_id')->nullable();
    $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
    
    $table->rememberToken();
    $table->timestamps();
});
    }

    public function down()
    {
        Schema::dropIfExists('portal_users');
    }
};