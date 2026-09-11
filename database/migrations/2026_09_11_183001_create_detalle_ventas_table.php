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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id('id_detalle');

            // Relación con la venta
            $table->foreignId('id_venta')
                  ->constrained(table: 'ventas', column: 'id_venta')
                  ->cascadeOnDelete();

            // Relación con el producto vendido
            $table->foreignId('id_producto')
                  ->nullable()
                  ->constrained(table: 'productos', column: 'id_producto')
                  ->nullOnDelete();

            $table->string('producto_nombre', 100);
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
