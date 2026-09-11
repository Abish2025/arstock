<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\MovimientoCuentaCorriente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cliente con deuda
        $cliente1 = Cliente::create([
            'nombre'         => 'María García',
            'telefono'       => '+54 9 11 4567-8901',
            'direccion'      => 'San Martín 450 (casa de rejas negras)',
            'saldo'          => 8500,
            'limite_credito' => 30000,
            'notas'          => 'Vecina de confianza, suele pasar a pagar los viernes.',
        ]);

        MovimientoCuentaCorriente::create([
            'id_cliente'     => $cliente1->id_cliente,
            'tipo'           => 'fiado',
            'monto'          => 12000,
            'concepto'       => 'Compra de mercadería y bebidas',
            'saldo_anterior' => 0,
            'saldo_nuevo'    => 12000,
            'created_at'     => now()->subDays(3),
        ]);

        MovimientoCuentaCorriente::create([
            'id_cliente'     => $cliente1->id_cliente,
            'tipo'           => 'pago',
            'monto'          => 3500,
            'concepto'       => 'Entrega en efectivo a cuenta',
            'saldo_anterior' => 12000,
            'saldo_nuevo'    => 8500,
            'created_at'     => now()->subDays(1),
        ]);

        // 2. Cliente al día
        $cliente2 = Cliente::create([
            'nombre'         => 'Carlos Rodríguez',
            'telefono'       => '+54 9 11 6543-2109',
            'direccion'      => 'Belgrano 1280',
            'saldo'          => 0,
            'limite_credito' => 20000,
            'notas'          => 'Siempre paga al contado o salda su cuenta en el día.',
        ]);

        MovimientoCuentaCorriente::create([
            'id_cliente'     => $cliente2->id_cliente,
            'tipo'           => 'fiado',
            'monto'          => 5000,
            'concepto'       => 'Compra de lácteos y fiambres',
            'saldo_anterior' => 0,
            'saldo_nuevo'    => 5000,
            'created_at'     => now()->subDays(5),
        ]);

        MovimientoCuentaCorriente::create([
            'id_cliente'     => $cliente2->id_cliente,
            'tipo'           => 'pago',
            'monto'          => 5000,
            'concepto'       => 'Pago total en efectivo',
            'saldo_anterior' => 5000,
            'saldo_nuevo'    => 0,
            'created_at'     => now()->subDays(2),
        ]);

        // 3. Cliente con saldo pequeño
        $cliente3 = Cliente::create([
            'nombre'         => 'Ana Martínez',
            'telefono'       => '+54 9 11 9876-5432',
            'direccion'      => 'Rivadavia 890 Dpto 2',
            'saldo'          => 2300,
            'limite_credito' => 15000,
            'notas'          => 'Le faltaron $2.300 el miércoles.',
        ]);

        MovimientoCuentaCorriente::create([
            'id_cliente'     => $cliente3->id_cliente,
            'tipo'           => 'fiado',
            'monto'          => 2300,
            'concepto'       => 'Saldo restante de compra',
            'saldo_anterior' => 0,
            'saldo_nuevo'    => 2300,
            'created_at'     => now()->subDays(2),
        ]);
    }
}
