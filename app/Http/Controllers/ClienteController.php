<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\MovimientoCuentaCorriente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if ($request->filled('filtro')) {
            if ($request->filtro === 'con_deuda') {
                $query->where('saldo', '>', 0);
            } elseif ($request->filtro === 'al_dia') {
                $query->where('saldo', '<=', 0);
            } elseif ($request->filtro === 'excedidos') {
                $query->whereNotNull('limite_credito')
                      ->whereColumn('saldo', '>', 'limite_credito');
            }
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
            'telefono'       => ['nullable', 'regex:/^\+?[0-9]{10,14}$/'],
            'direccion'      => 'nullable|max:255',
            'saldo'          => ['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'limite_credito' => ['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'notas'          => 'nullable|max:500',
        ], [
            'telefono.regex' => 'El teléfono debe ser un número válido de Argentina (Ej: 3764123456 o +5493764123456).',
            'saldo.min'      => 'El saldo inicial no puede ser negativo.',
            'limite_credito.min' => 'El límite de crédito no puede ser negativo.',
        ]);

        $saldoInicial = (float) ($validated['saldo'] ?? 0);

        DB::transaction(function () use ($validated, $saldoInicial) {
            $cliente = Cliente::create($validated);

            // Si se cargó con un saldo deudor previo, registrarlo en el historial
            if ($saldoInicial > 0) {
                MovimientoCuentaCorriente::create([
                    'id_cliente'     => $cliente->id_cliente,
                    'tipo'           => 'fiado',
                    'monto'          => $saldoInicial,
                    'concepto'       => 'Saldo deudor inicial / Carga previa',
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
            'telefono'       => ['nullable', 'regex:/^\+?[0-9]{10,14}$/'],
            'direccion'      => 'nullable|max:255',
            'limite_credito' => ['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'notas'          => 'nullable|max:500',
        ], [
            'telefono.regex' => 'El teléfono debe ser un número válido de Argentina (Ej: 3764123456 o +5493764123456).',
            'limite_credito.min' => 'El límite de crédito no puede ser negativo.',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
                         ->with('success', 'Datos del cliente actualizados.');
    }

    // DELETE /clientes/{cliente} → Eliminar cliente con verificación de operaciones
    public function destroy(Cliente $cliente)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede eliminar clientes.');
        }

        // Regla de integridad: No eliminar si tiene ventas o movimientos en cuenta corriente
        if ($cliente->tieneOperaciones()) {
            return back()->with('error', "No se puede eliminar a '{$cliente->nombre}' porque tiene historial de compras o movimientos en cuenta corriente. Para preservar los balances contables, el historial no se borra.");
        }

        $nombre = $cliente->nombre;
        $cliente->delete();

        return redirect()->route('clientes.index')
                         ->with('success', "Cliente '{$nombre}' eliminado correctamente.");
    }

    // POST /clientes/{cliente}/movimiento → Anotar fiado o registrar entrega/pago con control de límites y excedentes
    public function registrarMovimiento(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'tipo'     => 'required|in:fiado,pago',
            'monto'    => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'concepto' => 'required|max:255',
        ], [
            'monto.gt'    => 'El monto debe ser mayor a $0.',
            'monto.regex' => 'Formato de monto inválido (máximo 2 decimales).',
        ]);

        $monto = (float) $validated['monto'];

        // Control de límite de crédito para fiados
        if ($validated['tipo'] === 'fiado') {
            if ($cliente->limite_credito && $cliente->limite_credito > 0) {
                $saldoProyectado = (float)$cliente->saldo + $monto;
                if ($saldoProyectado > (float)$cliente->limite_credito) {
                    $disponible = max(0, (float)$cliente->limite_credito - (float)$cliente->saldo);
                    return back()->withInput()->with('error',
                        "El cliente superaría su límite de crédito de $" .
                        number_format($cliente->limite_credito, 2, ',', '.') .
                        ". Saldo actual: $" . number_format($cliente->saldo, 2, ',', '.') .
                        ". Monto disponible para fiar: $" . number_format($disponible, 2, ',', '.') . "."
                    );
                }
            }
        }

        // Control para pagos: evitar pagos mayores a la deuda
        if ($validated['tipo'] === 'pago') {
            if ((float)$cliente->saldo <= 0) {
                return back()->withInput()->with('error', "El cliente '{$cliente->nombre}' está al día y no posee deuda pendiente.");
            }

            if ($monto > (float)$cliente->saldo) {
                return back()->withInput()->with('error',
                    "El monto del pago ($" . number_format($monto, 2, ',', '.') .
                    ") es mayor que la deuda actual del cliente ($" .
                    number_format($cliente->saldo, 2, ',', '.') .
                    "). Ingresa un monto menor o igual a la deuda."
                );
            }
        }

        DB::transaction(function () use ($cliente, $validated, $monto) {
            $clienteBloqueado = Cliente::lockForUpdate()->find($cliente->id_cliente);
            $saldoAnterior = (float) $clienteBloqueado->saldo;

            if ($validated['tipo'] === 'fiado') {
                $saldoNuevo = $saldoAnterior + $monto;
            } else {
                $saldoNuevo = max(0, $saldoAnterior - $monto);
            }

            $clienteBloqueado->update(['saldo' => $saldoNuevo]);

            MovimientoCuentaCorriente::create([
                'id_cliente'     => $clienteBloqueado->id_cliente,
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
