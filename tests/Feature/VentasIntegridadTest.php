<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VentasIntegridadTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Producto $producto;
    protected Cliente $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('rol', 'admin')->first()
            ?? User::factory()->create(['rol' => 'admin', 'activo' => true]);

        $this->producto = Producto::create([
            'nombre'        => 'Producto Test Venta',
            'codigo'        => 'TEST-VTA-' . uniqid(),
            'precio_compra' => 500,
            'precio_venta'  => 800,
            'stock'         => 10,
            'stock_minimo'  => 3,
            'stock_critico' => 1,
        ]);

        $this->cliente = Cliente::create([
            'nombre'         => 'Cliente Test Venta',
            'saldo'          => 0,
            'limite_credito' => 5000,
        ]);
    }

    public function test_venta_valida_descuenta_stock_y_guarda_costo_historico(): void
    {
        $stockInicial = $this->producto->stock;

        $response = $this->actingAs($this->admin)->post('/ventas', [
            'metodo_pago' => 'efectivo',
            'monto_recibido' => 2000,
            'items' => [
                ['id_producto' => $this->producto->id_producto, 'cantidad' => 2],
            ],
        ]);

        $response->assertSessionHas('success');

        // Verificar que el stock disminuyó exactamente en 2 unidades
        $this->producto->refresh();
        $this->assertEquals($stockInicial - 2, $this->producto->stock);

        // Verificar cabecera y detalle en base de datos
        $venta = Venta::latest('id_venta')->first();
        $this->assertEquals(1600, $venta->total);
        $this->assertEquals(2000, $venta->monto_recibido);
        $this->assertEquals(400, $venta->vuelto);
        $this->assertEquals('completada', $venta->estado);

        $detalle = $venta->detalles()->first();
        $this->assertEquals(2, $detalle->cantidad);
        $this->assertEquals(800, $detalle->precio_unitario);
        $this->assertEquals(500, $detalle->costo_unitario); // Costo histórico preservado
    }

    public function test_venta_con_stock_insuficiente_es_rechazada(): void
    {
        $stockActual = $this->producto->stock;

        // Intentar comprar más del stock disponible
        $response = $this->actingAs($this->admin)->post('/ventas', [
            'metodo_pago' => 'efectivo',
            'items' => [
                ['id_producto' => $this->producto->id_producto, 'cantidad' => $stockActual + 5],
            ],
        ]);

        $response->assertStatus(422);

        // El stock no debe haber cambiado
        $this->producto->refresh();
        $this->assertEquals($stockActual, $this->producto->stock);
    }

    public function test_venta_fiada_actualiza_saldo_del_cliente_y_cuenta_corriente(): void
    {
        $response = $this->actingAs($this->admin)->post('/ventas', [
            'metodo_pago' => 'fiado',
            'id_cliente'  => $this->cliente->id_cliente,
            'items' => [
                ['id_producto' => $this->producto->id_producto, 'cantidad' => 1],
            ],
        ]);

        $response->assertSessionHas('success');

        $this->cliente->refresh();
        $this->assertEquals(800, $this->cliente->saldo);

        // Debe haberse creado el movimiento en cuenta corriente
        $this->assertDatabaseHas('movimientos_cuenta_corriente', [
            'id_cliente' => $this->cliente->id_cliente,
            'tipo'       => 'fiado',
            'monto'      => 800,
        ]);
    }

    public function test_venta_fiada_que_supera_limite_de_credito_es_bloqueada(): void
    {
        // El cliente tiene límite de $5000 y saldo 0. Intentamos venderle 10 unidades x $800 = $8000
        $this->producto->update(['stock' => 50]);

        $response = $this->actingAs($this->admin)->post('/ventas', [
            'metodo_pago' => 'fiado',
            'id_cliente'  => $this->cliente->id_cliente,
            'items' => [
                ['id_producto' => $this->producto->id_producto, 'cantidad' => 10],
            ],
        ]);

        $response->assertStatus(422);

        $this->cliente->refresh();
        $this->assertEquals(0, $this->cliente->saldo);
    }

    public function test_anulacion_de_venta_restaura_stock_y_revierte_saldo_fiado(): void
    {
        // 1. Crear venta fiada de 2 unidades
        $this->actingAs($this->admin)->post('/ventas', [
            'metodo_pago' => 'fiado',
            'id_cliente'  => $this->cliente->id_cliente,
            'items' => [
                ['id_producto' => $this->producto->id_producto, 'cantidad' => 2],
            ],
        ]);

        $venta = Venta::latest('id_venta')->first();
        $this->producto->refresh();
        $this->cliente->refresh();

        $stockDespuesDeVenta = $this->producto->stock;
        $saldoDespuesDeVenta = $this->cliente->saldo;

        // 2. Anular la venta
        $response = $this->actingAs($this->admin)->delete("/ventas/{$venta->id_venta}");
        $response->assertRedirect('/ventas');

        // 3. Verificar que el stock volvió
        $this->producto->refresh();
        $this->assertEquals($stockDespuesDeVenta + 2, $this->producto->stock);

        // 4. Verificar que la deuda del cliente se redujo
        $this->cliente->refresh();
        $this->assertEquals($saldoDespuesDeVenta - 1600, $this->cliente->saldo);

        // 5. Estado marcado como anulada
        $venta->refresh();
        $this->assertEquals('anulada', $venta->estado);
    }
}
