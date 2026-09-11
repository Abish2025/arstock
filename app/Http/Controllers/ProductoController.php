<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    // GET /productos → lista todos los productos, con buscador y paginado
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'proveedor'])->orderBy('nombre');

        // Si el usuario escribió algo en el buscador...
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('codigo', 'ilike', "%{$buscar}%");
            });
        }

        $productos = $query->paginate(10)->withQueryString();

        // Métricas de inventario para las tarjetas superiores
        $totalProductos = Producto::count();
        $stockBajo = Producto::whereColumn('stock', '<=', 'stock_minimo')->count();
        $valorInventario = Producto::selectRaw('COALESCE(SUM(stock * precio_compra), 0) as total')->value('total') ?? 0;

        return view('productos.index', compact('productos', 'totalProductos', 'stockBajo', 'valorInventario'));
    }

    // GET /productos/create → muestra el formulario de carga
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('empresa')->get();

        return view('productos.create', compact('categorias', 'proveedores'));
    }

    // POST /productos → guarda el nuevo producto
    public function store(Request $request)
    {
        $validated = $this->validarProducto($request);

        Producto::create($validated);

        return redirect()->route('productos.index')
                         ->with('success', 'Producto creado correctamente.');
    }

    // GET /productos/{producto}/edit → muestra el formulario con los datos cargados
    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('empresa')->get();

        return view('productos.edit', compact('producto', 'categorias', 'proveedores'));
    }

    // PUT /productos/{producto} → actualiza el producto existente
    public function update(Request $request, Producto $producto)
    {
        $validated = $this->validarProducto($request, $producto->id_producto);

        $producto->update($validated);

        return redirect()->route('productos.index')
                         ->with('success', 'Producto actualizado correctamente.');
    }

    // DELETE /productos/{producto} → elimina el producto
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
                         ->with('success', 'Producto eliminado correctamente.');
    }

    // Método privado de validación
    private function validarProducto(Request $request, ?int $idProducto = null): array
    {
        return $request->validate([
            'nombre'         => 'required|min:2|max:100',
            'codigo'         => [
                'required',
                'max:25',
                Rule::unique('productos', 'codigo')->ignore($idProducto, 'id_producto'),
            ],
            'descripcion'    => 'nullable|max:255',
            'precio_compra'  => 'required|numeric|min:0',
            'precio_venta'   => 'required|numeric|min:0|gte:precio_compra',
            'stock'          => 'required|integer|min:0',
            'stock_minimo'   => 'required|integer|min:0',
            'stock_critico'  => 'required|integer|min:0|lte:stock_minimo',
            'id_categoria'   => 'nullable|exists:categorias,id_categoria',
            'id_proveedor'   => 'nullable|exists:proveedores,id_proveedor',
        ], [
            'precio_venta.gte'  => 'El precio de venta no puede ser menor al de compra.',
            'stock_critico.lte' => 'El stock crítico no puede ser mayor al stock mínimo.',
        ]);
    }
}