<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductosYStockTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('rol', 'admin')->first()
            ?? User::factory()->create(['rol' => 'admin', 'activo' => true]);
    }

    public function test_creacion_de_producto_con_stock_inicial_registra_auditoria(): void
    {
        $codigo = 'PRD-' . uniqid();

        $response = $this->actingAs($this->admin)->post('/productos', [
            'nombre'        => 'Galletitas de Chocolate',
            'codigo'        => $codigo,
            'precio_compra' => 450,
            'precio_venta'  => 700,
            'stock'         => 20,
            'stock_minimo'  => 5,
            'stock_critico' => 2,
        ]);

        $response->assertRedirect('/productos');

        $producto = Producto::where('codigo', $codigo)->first();
        $this->assertNotNull($producto);
        $this->assertEquals(20, $producto->stock);

        // Se debe haber creado el movimiento de stock
        $this->assertDatabaseHas('movimientos_stock', [
            'id_producto' => $producto->id_producto,
            'tipo'        => 'entrada',
            'cantidad'    => 20,
        ]);
    }

    public function test_ajuste_manual_de_merma_descuenta_stock(): void
    {
        $producto = Producto::create([
            'nombre'        => 'Producto Perecedero',
            'codigo'        => 'PER-' . uniqid(),
            'precio_compra' => 100,
            'precio_venta'  => 200,
            'stock'         => 15,
            'stock_minimo'  => 3,
            'stock_critico' => 1,
        ]);

        $response = $this->actingAs($this->admin)->post('/stock/ajuste', [
            'id_producto' => $producto->id_producto,
            'tipo'        => 'merma',
            'cantidad'    => 3,
            'motivo'      => 'Vencimiento de lote',
        ]);

        $response->assertRedirect('/stock');

        $producto->refresh();
        $this->assertEquals(12, $producto->stock);

        $this->assertDatabaseHas('movimientos_stock', [
            'id_producto' => $producto->id_producto,
            'tipo'        => 'merma',
            'cantidad'    => 3,
            'stock_nuevo' => 12,
        ]);
    }

    public function test_producto_con_ventas_asociadas_no_puede_eliminarse(): void
    {
        $producto = Producto::create([
            'nombre'        => 'Producto Vendido Histórico',
            'codigo'        => 'HIST-' . uniqid(),
            'precio_compra' => 300,
            'precio_venta'  => 500,
            'stock'         => 5,
            'stock_minimo'  => 1,
            'stock_critico' => 0,
        ]);

        $venta = Venta::create([
            'codigo'      => 'VT-TEST-' . uniqid(),
            'total'       => 500,
            'metodo_pago' => 'efectivo',
            'estado'      => 'completada',
        ]);

        DetalleVenta::create([
            'id_venta'        => $venta->id_venta,
            'id_producto'     => $producto->id_producto,
            'producto_nombre' => $producto->nombre,
            'cantidad'        => 1,
            'precio_unitario' => 500,
            'costo_unitario'  => 300,
            'subtotal'        => 500,
        ]);

        // Intentar eliminar
        $response = $this->actingAs($this->admin)->delete("/productos/{$producto->id_producto}");
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('productos', ['id_producto' => $producto->id_producto]);
    }
}
