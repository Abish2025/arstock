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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('id_cliente');
            $table->string('nombre', 100);
            $table->string('telefono', 25)->nullable();
            $table->string('direccion', 255)->nullable();
            // Saldo adeudado: si es mayor a 0, el cliente debe plata (fiado)
            $table->decimal('saldo', 10, 2)->default(0);
            $table->decimal('limite_credito', 10, 2)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
