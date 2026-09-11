<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;

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

    // GET /proveedores/create → Formulario de alta
    public function create()
    {
        return view('proveedores.create');
    }

    // POST /proveedores → Guardar nuevo proveedor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa'     => 'required|min:2|max:100',
            'contacto'    => 'nullable|max:100',
            'telefono'    => 'nullable|max:25',
            'email'       => 'nullable|email|max:100',
            'direccion'   => 'nullable|max:255',
            'cuit'        => 'nullable|max:25',
            'dias_visita' => 'nullable|max:100',
            'notas'       => 'nullable|max:500',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
                         ->with('success', 'Proveedor registrado correctamente.');
    }

    // GET /proveedores/{proveedor} → Ficha y catálogo de productos provistos
    public function show(Proveedor $proveedor)
    {
        $proveedor->load(['productos.categoria']);
        return view('proveedores.show', compact('proveedor'));
    }

    // GET /proveedores/{proveedor}/edit → Formulario de edición
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    // PUT /proveedores/{proveedor} → Actualizar datos
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'empresa'     => 'required|min:2|max:100',
            'contacto'    => 'nullable|max:100',
            'telefono'    => 'nullable|max:25',
            'email'       => 'nullable|email|max:100',
            'direccion'   => 'nullable|max:255',
            'cuit'        => 'nullable|max:25',
            'dias_visita' => 'nullable|max:100',
            'notas'       => 'nullable|max:500',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
                         ->with('success', 'Datos del proveedor actualizados.');
    }

    // DELETE /proveedores/{proveedor} → Eliminar proveedor
    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('proveedores.index')
                         ->with('success', 'Proveedor eliminado correctamente.');
    }
}
