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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id('id_proveedor');
            $table->string('empresa', 100);
            $table->string('contacto', 100)->nullable(); // Preventista / Vendedor
            $table->string('telefono', 25)->nullable();   // WhatsApp para pedidos
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('cuit', 25)->nullable();
            $table->string('dias_visita', 100)->nullable(); // Ej: "Martes y Jueves"
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
