<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('id_user')->nullable()->after('id_venta')->constrained('users')->nullOnDelete();
            $table->foreignId('id_caja')->nullable()->after('id_user')->constrained('cajas', 'id_caja')->nullOnDelete();
            $table->decimal('monto_recibido', 12, 2)->default(0)->after('total');
            $table->decimal('vuelto', 12, 2)->default(0)->after('monto_recibido');
            $table->string('referencia_pago', 100)->nullable()->after('vuelto');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_caja']);
            $table->dropColumn(['id_user', 'id_caja', 'monto_recibido', 'vuelto', 'referencia_pago']);
        });
    }
};
