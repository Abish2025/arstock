<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    // GET /proveedores → Directorio de proveedores con KPIs y buscador
    public function index(Request $request)
    {
        $query = Proveedor::withCount('productos')->orderBy('empresa');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('empresa', 'ilike', "%{$buscar}%")
                  ->orWhere('contacto', 'ilike', "%{$buscar}%")
                  ->orWhere('telefono', 'ilike', "%{$buscar}%")
                  ->orWhere('dias_visita', 'ilike', "%{$buscar}%");
            });
        }

        $proveedores = $query->paginate(10)->withQueryString();

        // KPIs superiores
        $totalProveedores = Proveedor::count();
        $proveedoresActivos = Proveedor::has('productos')->count();
        $totalProductosAsignados = Producto::whereNotNull('id_proveedor')->count();

        return view('proveedores.index', compact(
            'proveedores',
            'totalProveedores',
            'proveedoresActivos',
            'totalProductosAsignados'
        ));
    }

    // GET /proveedores/create → Formulario de alta (Solo Admin)
    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede agregar proveedores.');
        }

        return view('proveedores.create');
    }

    // POST /proveedores → Guardar nuevo proveedor (Solo Admin)
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede agregar proveedores.');
        }

        $validated = $request->validate([
            'empresa'     => 'required|min:2|max:100',
            'contacto'    => 'nullable|max:100',
            'telefono'    => ['nullable', 'regex:/^\+?[0-9]{10,14}$/'],
            'email'       => 'nullable|email|max:100',
            'direccion'   => 'nullable|max:255',
            'cuit'        => 'nullable|max:25',
            'dias_visita' => 'nullable|max:100',
            'notas'       => 'nullable|max:500',
        ], [
            'telefono.regex' => 'El teléfono debe ser un número válido de Argentina (Ej: 3764123456 o +5493764123456).',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
                         ->with('success', 'Proveedor registrado correctamente.');
    }

    // GET /proveedores/{proveedor} → Ficha y catálogo de productos provistos
    public function show(Proveedor $proveedor)
    {
        $proveedor->load(['productos.categoria', 'compras']);
        return view('proveedores.show', compact('proveedor'));
    }

    // GET /proveedores/{proveedor}/edit → Formulario de edición (Solo Admin)
    public function edit(Proveedor $proveedor)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede editar proveedores.');
        }

        return view('proveedores.edit', compact('proveedor'));
    }

    // PUT /proveedores/{proveedor} → Actualizar datos (Solo Admin)
    public function update(Request $request, Proveedor $proveedor)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede editar proveedores.');
        }

        $validated = $request->validate([
            'empresa'     => 'required|min:2|max:100',
            'contacto'    => 'nullable|max:100',
            'telefono'    => ['nullable', 'regex:/^\+?[0-9]{10,14}$/'],
            'email'       => 'nullable|email|max:100',
            'direccion'   => 'nullable|max:255',
            'cuit'        => 'nullable|max:25',
            'dias_visita' => 'nullable|max:100',
            'notas'       => 'nullable|max:500',
        ], [
            'telefono.regex' => 'El teléfono debe ser un número válido de Argentina (Ej: 3764123456 o +5493764123456).',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
                         ->with('success', 'Datos del proveedor actualizados.');
    }

    // DELETE /proveedores/{proveedor} → Eliminar proveedor con verificación de integridad (Solo Admin)
    public function destroy(Proveedor $proveedor)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede eliminar proveedores.');
        }

        // Regla de integridad: No eliminar si tiene productos asignados o compras registradas
        if ($proveedor->tieneOperaciones()) {
            return back()->with('error', "No se puede eliminar el proveedor '{$proveedor->empresa}' porque tiene productos o ingresos de mercadería asociados en el historial.");
        }

        $empresa = $proveedor->empresa;
        $proveedor->delete();

        return redirect()->route('proveedores.index')
                         ->with('success', "Proveedor '{$empresa}' eliminado correctamente.");
    }
}
