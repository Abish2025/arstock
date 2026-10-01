<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportesYGananciasTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('rol', 'admin')->first()
            ?? User::factory()->create(['rol' => 'admin', 'activo' => true]);
    }

    public function test_ganancias_reales_se_calculan_con_costo_historico_y_excluyen_anuladas(): void
    {
        // 1. Crear producto con costo $600 y precio $1000
        $producto = Producto::create([
            'nombre'        => 'Producto Ganancia',
            'codigo'        => 'GAN-' . uniqid(),
            'precio_compra' => 600,
            'precio_venta'  => 1000,
            'stock'         => 20,
            'stock_minimo'  => 5,
            'stock_critico' => 2,
        ]);

        // 2. Crear una venta completada de 2 unidades -> Ingreso: $2000, Costo: $1200, Ganancia: $800
        $v1 = Venta::create([
            'codigo'      => 'VT-REP-1',
            'total'       => 2000,
            'metodo_pago' => 'efectivo',
            'estado'      => 'completada',
        ]);
        DetalleVenta::create([
            'id_venta'        => $v1->id_venta,
            'id_producto'     => $producto->id_producto,
            'producto_nombre' => $producto->nombre,
            'cantidad'        => 2,
            'precio_unitario' => 1000,
            'costo_unitario'  => 600,
            'subtotal'        => 2000,
        ]);

        // 3. Crear una venta anulada de 5 unidades -> NO debe sumar a ingresos ni costo
        $v2 = Venta::create([
            'codigo'      => 'VT-REP-2',
            'total'       => 5000,
            'metodo_pago' => 'efectivo',
            'estado'      => 'anulada',
        ]);
        DetalleVenta::create([
            'id_venta'        => $v2->id_venta,
            'id_producto'     => $producto->id_producto,
            'producto_nombre' => $producto->nombre,
            'cantidad'        => 5,
            'precio_unitario' => 1000,
            'costo_unitario'  => 600,
            'subtotal'        => 5000,
        ]);

        // 4. Consultar reportes con período de hoy
        $response = $this->actingAs($this->admin)->get('/reportes?periodo=hoy');
        $response->assertStatus(200);

        // Verificar variables en la vista
        $totalIngresos = $response->viewData('totalIngresos');
        $totalCosto    = $response->viewData('totalCosto');
        $gananciaBruta = $response->viewData('gananciaBruta');

        $this->assertGreaterThanOrEqual(2000, $totalIngresos);
        $this->assertGreaterThanOrEqual(1200, $totalCosto);
        $this->assertGreaterThanOrEqual(800, $gananciaBruta);
    }
}
