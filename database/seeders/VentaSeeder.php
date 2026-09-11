<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        $producto1 = Producto::first();
        $producto2 = Producto::skip(1)->first() ?? $producto1;
        $cliente1 = Cliente::first();
        $cliente2 = Cliente::skip(1)->first() ?? $cliente1;

        if (!$producto1) return;

        // Venta 1: Completada en efectivo
        $v1 = Venta::create([
            'codigo'         => 'VT-001',
            'id_cliente'     => $cliente1 ? $cliente1->id_cliente : null,
            'cliente_nombre' => $cliente1 ? $cliente1->nombre : 'María García',
            'metodo_pago'    => 'efectivo',
            'total'          => $producto1->precio_venta,
            'estado'         => 'completada',
            'created_at'     => now()->subHours(5),
        ]);
        DetalleVenta::create([
            'id_venta'        => $v1->id_venta,
            'id_producto'     => $producto1->id_producto,
            'producto_nombre' => $producto1->nombre,
            'cantidad'        => 1,
            'precio_unitario' => $producto1->precio_venta,
            'subtotal'        => $producto1->precio_venta,
            'created_at'      => now()->subHours(5),
        ]);

        // Venta 2: Completada en transferencia
        $v2 = Venta::create([
            'codigo'         => 'VT-002',
            'id_cliente'     => $cliente2 ? $cliente2->id_cliente : null,
            'cliente_nombre' => $cliente2 ? $cliente2->nombre : 'Carlos Rodríguez',
            'metodo_pago'    => 'transferencia',
            'total'          => $producto2->precio_venta * 2,
            'estado'         => 'completada',
            'created_at'     => now()->subHours(3),
        ]);
        DetalleVenta::create([
            'id_venta'        => $v2->id_venta,
            'id_producto'     => $producto2->id_producto,
            'producto_nombre' => $producto2->nombre,
            'cantidad'        => 2,
            'precio_unitario' => $producto2->precio_venta,
            'subtotal'        => $producto2->precio_venta * 2,
            'created_at'      => now()->subHours(3),
        ]);

        // Venta 3: Fiado pendiente
        $v3 = Venta::create([
            'codigo'         => 'VT-003',
            'id_cliente'     => $cliente1 ? $cliente1->id_cliente : null,
            'cliente_nombre' => $cliente1 ? $cliente1->nombre : 'María García',
            'metodo_pago'    => 'fiado',
            'total'          => $producto1->precio_venta,
            'estado'         => 'pendiente',
            'created_at'     => now()->subHours(1),
        ]);
        DetalleVenta::create([
            'id_venta'        => $v3->id_venta,
            'id_producto'     => $producto1->id_producto,
            'producto_nombre' => $producto1->nombre,
            'cantidad'        => 1,
            'precio_unitario' => $producto1->precio_venta,
            'subtotal'        => $producto1->precio_venta,
            'created_at'      => now()->subHours(1),
        ]);

        // Venta 4: Consumidor final
        $v4 = Venta::create([
            'codigo'         => 'VT-004',
            'id_cliente'     => null,
            'cliente_nombre' => 'Consumidor Final',
            'metodo_pago'    => 'tarjeta',
            'total'          => $producto2->precio_venta,
            'estado'         => 'completada',
            'created_at'     => now()->subMinutes(20),
        ]);
        DetalleVenta::create([
            'id_venta'        => $v4->id_venta,
            'id_producto'     => $producto2->id_producto,
            'producto_nombre' => $producto2->nombre,
            'cantidad'        => 1,
            'precio_unitario' => $producto2->precio_venta,
            'subtotal'        => $producto2->precio_venta,
            'created_at'      => now()->subMinutes(20),
        ]);
    }
}
