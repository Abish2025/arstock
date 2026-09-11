<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\MovimientoCuentaCorriente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    // GET /clientes → Lista de clientes con buscador, paginación y KPIs
    public function index(Request $request)
    {
        $query = Cliente::orderBy('nombre');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('telefono', 'ilike', "%{$buscar}%")
                  ->orWhere('direccion', 'ilike', "%{$buscar}%");
            });
        }

        $clientes = $query->paginate(10)->withQueryString();

        // Métricas para las tarjetas superiores
        $totalClientes = Cliente::count();
        $clientesConDeuda = Cliente::where('saldo', '>', 0)->count();
        $totalDeuda = Cliente::where('saldo', '>', 0)->sum('saldo');

        return view('clientes.index', compact('clientes', 'totalClientes', 'clientesConDeuda', 'totalDeuda'));
    }

    // GET /clientes/create → Formulario de alta
    public function create()
    {
        return view('clientes.create');
    }

    // POST /clientes → Guardar nuevo cliente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'         => 'required|min:2|max:100',
            'telefono'       => 'nullable|max:25',
            'direccion'      => 'nullable|max:255',
            'saldo'          => 'nullable|numeric|min:0',
            'limite_credito' => 'nullable|numeric|min:0',
            'notas'          => 'nullable|max:500',
        ]);

        $saldoInicial = $validated['saldo'] ?? 0;

        DB::transaction(function () use ($validated, $saldoInicial) {
            $cliente = Cliente::create($validated);

            // Si se cargó con un saldo deudor previo, registrarlo en el historial
            if ($saldoInicial > 0) {
                MovimientoCuentaCorriente::create([
                    'id_cliente'     => $cliente->id_cliente,
                    'tipo'           => 'fiado',
                    'monto'          => $saldoInicial,
                    'concepto'       => 'Saldo anterior / Carga inicial',
                    'saldo_anterior' => 0,
                    'saldo_nuevo'    => $saldoInicial,
                ]);
            }
        });

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente registrado correctamente.');
    }

    // GET /clientes/{cliente} → Ficha de cuenta corriente y cuaderno de fiados
    public function show(Cliente $cliente)
    {
        $movimientos = $cliente->movimientos()->paginate(15);
        return view('clientes.show', compact('cliente', 'movimientos'));
    }

    // GET /clientes/{cliente}/edit → Formulario de edición
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    // PUT /clientes/{cliente} → Actualizar datos
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre'         => 'required|min:2|max:100',
            'telefono'       => 'nullable|max:25',
            'direccion'      => 'nullable|max:255',
            'limite_credito' => 'nullable|numeric|min:0',
            'notas'          => 'nullable|max:500',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
                         ->with('success', 'Datos del cliente actualizados.');
    }

    // DELETE /clientes/{cliente} → Eliminar cliente
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente eliminado correctamente.');
    }

    // POST /clientes/{cliente}/movimiento → Anotar fiado o registrar entrega/pago
    public function registrarMovimiento(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'tipo'     => 'required|in:fiado,pago',
            'monto'    => 'required|numeric|min:0.01',
            'concepto' => 'required|max:255',
        ]);

        DB::transaction(function () use ($cliente, $validated) {
            $saldoAnterior = $cliente->saldo;
            $monto = $validated['monto'];

            if ($validated['tipo'] === 'fiado') {
                $saldoNuevo = $saldoAnterior + $monto;
                $mensajeAccion = 'Fiado anotado correctamente.';
            } else {
                // Pago: reduce la deuda
                $saldoNuevo = max(0, $saldoAnterior - $monto);
                $mensajeAccion = 'Pago / Entrega registrada correctamente.';
            }

            $cliente->update(['saldo' => $saldoNuevo]);

            MovimientoCuentaCorriente::create([
                'id_cliente'     => $cliente->id_cliente,
                'tipo'           => $validated['tipo'],
                'monto'          => $monto,
                'concepto'       => $validated['concepto'],
                'saldo_anterior' => $saldoAnterior,
                'saldo_nuevo'    => $saldoNuevo,
            ]);
        });

        return redirect()->route('clientes.show', $cliente)
                         ->with('success', $validated['tipo'] === 'pago' ? 'Pago registrado correctamente.' : 'Fiado anotado con éxito.');
    }
}
