<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;
use App\Models\Producto;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $prov1 = Proveedor::create([
            'empresa'     => 'Distribuidora Mayorista Central',
            'contacto'    => 'Juan Pérez (Preventista)',
            'telefono'    => '+54 9 11 4455-6677',
            'email'       => 'ventas@distribuidoracentral.com',
            'direccion'   => 'Av. Corrientes 3420, CABA',
            'cuit'        => '30-71234567-8',
            'dias_visita' => 'Lunes y Jueves',
            'notas'       => 'Pedidos con 24 horas de anticipación. Descuento del 5% al contado.',
        ]);

        $prov2 = Proveedor::create([
            'empresa'     => 'Logística & Insumos del Sur',
            'contacto'    => 'Mariana Gómez',
            'telefono'    => '+54 9 11 5566-7788',
            'email'       => 'contacto@insumosdelsur.com.ar',
            'direccion'   => 'Calle Belgrano 1250, Avellaneda',
            'cuit'        => '30-65432198-4',
            'dias_visita' => 'Miércoles',
            'notas'       => 'Entrega en turno mañana de 08:00 a 12:00 hs.',
        ]);

        $prov3 = Proveedor::create([
            'empresa'     => 'TecnoStock Argentina',
            'contacto'    => 'Lucas Rodríguez',
            'telefono'    => '+54 9 11 3322-1100',
            'email'       => 'lucas@tecnostock.com.ar',
            'direccion'   => 'San Martín 890, San Justo',
            'cuit'        => '33-88997766-9',
            'dias_visita' => 'Viernes',
            'notas'       => 'Distribuidor oficial de periféricos e informática. Garantía oficial.',
        ]);

        // Asignamos el proveedor de tecnología a los productos existentes
        Producto::query()->update(['id_proveedor' => $prov3->id_proveedor]);
    }
}
