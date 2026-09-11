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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id('id_venta');
            $table->string('codigo', 25)->unique(); // Código visible tipo VT-001
            
            // Relación con cliente (puede ser nulo si es Consumidor Final)
            $table->foreignId('id_cliente')
                  ->nullable()
                  ->constrained(table: 'clientes', column: 'id_cliente')
                  ->nullOnDelete();

            $table->string('cliente_nombre', 100)->default('Consumidor Final');
            
            // Método de pago: 'efectivo', 'transferencia', 'tarjeta', 'fiado'
            $table->string('metodo_pago', 30)->default('efectivo');
            $table->decimal('total', 10, 2)->default(0);
            
            // Estado: 'completada' (pagada), 'pendiente' (fiada por cobrar), 'anulada'
            $table->string('estado', 20)->default('completada');
            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
