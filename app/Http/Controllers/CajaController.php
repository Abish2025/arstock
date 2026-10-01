<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    // GET /cajas → Listado de turnos y estado de la caja actual
    public function index(Request $request)
    {
        $user = Auth::user();

        // Caja actualmente abierta para el usuario logueado
        $cajaActual = Caja::where('id_user', $user->id)
                          ->where('estado', 'abierta')
                          ->latest('fecha_apertura')
                          ->first();

        // Resumen en vivo de la caja actual (si está abierta)
        $resumenActual = null;
        if ($cajaActual) {
            $ventasCaja = Venta::where('id_caja', $cajaActual->id_caja)
                               ->where('estado', '!=', 'anulada')
                               ->get();

            $totalEfectivo = $ventasCaja->where('metodo_pago', 'efectivo')->sum('total');
            $totalTransf   = $ventasCaja->where('metodo_pago', 'transferencia')->sum('total');
            $totalTarjeta  = $ventasCaja->where('metodo_pago', 'tarjeta')->sum('total');
            $totalFiado    = $ventasCaja->where('metodo_pago', 'fiado')->sum('total');

            $resumenActual = [
                'efectivo'        => $totalEfectivo,
                'transferencia'   => $totalTransf,
                'tarjeta'         => $totalTarjeta,
                'fiado'           => $totalFiado,
                'total_cobrado'   => $totalEfectivo + $totalTransf + $totalTarjeta,
                'esperado_caja'   => (float)$cajaActual->monto_apertura + (float)$totalEfectivo,
                'cantidad_ventas' => $ventasCaja->count(),
            ];
        }

        // Historial de cajas anteriores (si es admin ve todas, si es cajero ve solo las suyas)
        $historialQuery = Caja::with('usuario')->orderByDesc('fecha_apertura');
        if (!$user->isAdmin()) {
            $historialQuery->where('id_user', $user->id);
        }

        $historialCajas = $historialQuery->paginate(10);

        return view('cajas.index', compact('cajaActual', 'resumenActual', 'historialCajas'));
    }

    // POST /cajas/abrir → Apertura de caja con fondo inicial
    public function abrir(Request $request)
    {
        $user = Auth::user();

        // Verificar si ya tiene una caja abierta
        $cajaAbierta = Caja::where('id_user', $user->id)
                           ->where('estado', 'abierta')
                           ->exists();

        if ($cajaAbierta) {
            return back()->with('error', 'Ya tienes un turno de caja abierto.');
        }

        $validated = $request->validate([
            'monto_apertura' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'notas_apertura' => 'nullable|string|max:255',
        ], [
            'monto_apertura.required' => 'Debes indicar el monto o fondo inicial de caja.',
            'monto_apertura.min'      => 'El fondo inicial no puede ser negativo.',
        ]);

        Caja::create([
            'id_user'        => $user->id,
            'monto_apertura' => $validated['monto_apertura'],
            'fecha_apertura' => now(),
            'estado'         => 'abierta',
            'notas_apertura' => $validated['notas_apertura'] ?? null,
        ]);

        return redirect()->route('cajas.index')
                         ->with('success', 'Turno de caja abierto exitosamente. Ya puedes registrar ventas.');
    }

    // GET /cajas/{caja} → Detalle del turno de caja y arqueo
    public function show(Caja $caja)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $caja->id_user !== $user->id) {
            abort(403, 'No tienes permiso para ver esta caja.');
        }

        $caja->load(['usuario', 'ventas.detalles']);

        $ventasValidas = $caja->ventas->where('estado', '!=', 'anulada');
        $ventasEfectivo = $ventasValidas->where('metodo_pago', 'efectivo')->sum('total');
        $ventasTransf   = $ventasValidas->where('metodo_pago', 'transferencia')->sum('total');
        $ventasTarjeta  = $ventasValidas->where('metodo_pago', 'tarjeta')->sum('total');
        $ventasFiado    = $ventasValidas->where('metodo_pago', 'fiado')->sum('total');

        $esperadoEfectivo = (float)$caja->monto_apertura + (float)$ventasEfectivo;

        return view('cajas.show', compact(
            'caja',
            'ventasValidas',
            'ventasEfectivo',
            'ventasTransf',
            'ventasTarjeta',
            'ventasFiado',
            'esperadoEfectivo'
        ));
    }

    // POST /cajas/{caja}/cerrar → Cierre de caja y arqueo final
    public function cerrar(Request $request, Caja $caja)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $caja->id_user !== $user->id) {
            abort(403, 'No tienes permiso para cerrar esta caja.');
        }

        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'Esta caja ya se encuentra cerrada.');
        }

        $validated = $request->validate([
            'monto_cierre_real' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'notas_cierre'      => 'nullable|string|max:500',
        ], [
            'monto_cierre_real.required' => 'Debes ingresar el efectivo contado físicamente en la caja.',
            'monto_cierre_real.min'      => 'El monto de cierre no puede ser negativo.',
        ]);

        DB::transaction(function () use ($caja, $validated) {
            // Calcular lo esperado en efectivo
            $ventasEfectivo = Venta::where('id_caja', $caja->id_caja)
                                   ->where('estado', '!=', 'anulada')
                                   ->where('metodo_pago', 'efectivo')
                                   ->sum('total');

            $esperado = (float)$caja->monto_apertura + (float)$ventasEfectivo;
            $real     = (float)$validated['monto_cierre_real'];
            $dif      = $real - $esperado;

            $caja->update([
                'monto_cierre_esperado' => $esperado,
                'monto_cierre_real'     => $real,
                'diferencia'            => $dif,
                'fecha_cierre'          => now(),
                'estado'                => 'cerrada',
                'notas_cierre'          => $validated['notas_cierre'] ?? null,
            ]);
        });

        return redirect()->route('cajas.show', $caja)
                         ->with('success', 'Turno de caja cerrado correctamente.');
    }
}
