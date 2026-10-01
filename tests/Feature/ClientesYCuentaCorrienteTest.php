<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cliente;
use App\Models\MovimientoCuentaCorriente;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClientesYCuentaCorrienteTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('rol', 'admin')->first()
            ?? User::factory()->create(['rol' => 'admin', 'activo' => true]);
    }

    public function test_crear_cliente_con_saldo_cero(): void
    {
        $response = $this->actingAs($this->admin)->post('/clientes', [
            'nombre'         => 'Cliente Nuevo Test',
            'telefono'       => '3764123456',
            'saldo'          => 0,
            'limite_credito' => 10000,
        ]);

        $response->assertRedirect('/clientes');
        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Cliente Nuevo Test',
            'saldo'  => 0,
        ]);
    }

    public function test_pago_reduce_deuda_del_cliente(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Cliente Deudor',
            'saldo'  => 2500,
        ]);

        $response = $this->actingAs($this->admin)->post("/clientes/{$cliente->id_cliente}/movimiento", [
            'tipo'     => 'pago',
            'monto'    => 1000,
            'concepto' => 'Entrega en efectivo a cuenta',
        ]);

        $response->assertRedirect("/clientes/{$cliente->id_cliente}");

        $cliente->refresh();
        $this->assertEquals(1500, $cliente->saldo);

        $this->assertDatabaseHas('movimientos_cuenta_corriente', [
            'id_cliente'     => $cliente->id_cliente,
            'tipo'           => 'pago',
            'monto'          => 1000,
            'saldo_anterior' => 2500,
            'saldo_nuevo'    => 1500,
        ]);
    }

    public function test_pago_mayor_a_la_deuda_es_rechazado(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Cliente Deudor Menor',
            'saldo'  => 500,
        ]);

        // Intentar pagar $1000 cuando la deuda es de $500
        $response = $this->actingAs($this->admin)->post("/clientes/{$cliente->id_cliente}/movimiento", [
            'tipo'     => 'pago',
            'monto'    => 1000,
            'concepto' => 'Pago excesivo',
        ]);

        $response->assertSessionHas('error');

        $cliente->refresh();
        $this->assertEquals(500, $cliente->saldo);
    }

    public function test_cliente_con_historial_no_puede_ser_eliminado(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Cliente Con Historial',
            'saldo'  => 0,
        ]);

        MovimientoCuentaCorriente::create([
            'id_cliente'     => $cliente->id_cliente,
            'tipo'           => 'pago',
            'monto'          => 200,
            'concepto'       => 'Pago previo',
            'saldo_anterior' => 200,
            'saldo_nuevo'    => 0,
        ]);

        $response = $this->actingAs($this->admin)->delete("/clientes/{$cliente->id_cliente}");
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('clientes', ['id_cliente' => $cliente->id_cliente]);
    }
}
