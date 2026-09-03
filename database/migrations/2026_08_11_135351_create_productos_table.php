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
    Schema::create('productos', function (Blueprint $table) {
        // Clave primaria con nombre id_producto
        $table->id('id_producto');

        // Nombre del producto (máx. 25 caracteres, según diagrama)
        $table->string('nombre', 25);

        // Código único del producto (ej: código de barras o SKU)
        // unique() evita que se carguen dos productos con el mismo código
        $table->string('codigo', 25)->unique();

        // Descripción opcional, por eso nullable()
        $table->string('descripcion', 255)->nullable();

        // decimal(10,2) = hasta 10 dígitos en total, 2 después de la coma
        // Es el formato correcto para plata (evita errores de redondeo)
        $table->decimal('precio_compra', 10, 2);
        $table->decimal('precio_venta', 10, 2);

        // Cantidad actual en stock, arranca en 0 por defecto
        $table->integer('stock')->default(0);

        // A partir de qué cantidad se considera "stock bajo"
        $table->integer('stock_minimo')->default(0);

        // A partir de qué cantidad se considera "stock crítico" (peor que bajo)
        $table->integer('stock_critico')->default(0);

        // Clave foránea hacia categorias.id_categoria
        // nullable() = un producto puede no tener categoría asignada
        // constrained() = crea la relación FK real en la base de datos
        // nullOnDelete() = si se borra la categoría, el producto queda sin categoría (no se borra el producto)
        $table->foreignId('id_categoria')
              ->nullable()
              ->constrained(table: 'categorias', column: 'id_categoria')
              ->nullOnDelete();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('productos');
}
};
