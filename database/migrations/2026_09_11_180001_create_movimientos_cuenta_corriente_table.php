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
        Schema::create('movimientos_cuenta_corriente', function (Blueprint $table) {
            $table->id('id_movimiento');
            
            // Relación con el cliente (si se borra el cliente, se borra su historial)
            $table->foreignId('id_cliente')
                  ->constrained(table: 'clientes', column: 'id_cliente')
                  ->cascadeOnDelete();

            // tipo: 'fiado' suma a la deuda, 'pago' descuenta de la deuda
            $table->string('tipo', 20); // 'fiado' o 'pago'
            $table->decimal('monto', 10, 2);
            $table->string('concepto', 255); // Ej: "Fiado de mercadería", "Pago en efectivo"

            // Trazabilidad de saldos para transparencia total
            $table->decimal('saldo_anterior', 10, 2);
            $table->decimal('saldo_nuevo', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_cuenta_corriente');
    }
};
