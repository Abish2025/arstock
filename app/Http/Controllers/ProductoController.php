<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    // GET /productos → lista todos los productos, con buscador, filtros de stock y paginado
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'proveedor'])->orderBy('nombre');

        // Buscador por nombre o código
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('codigo', 'ilike', "%{$buscar}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        // Filtro por estado de stock
        if ($request->filled('alerta')) {
            if ($request->alerta === 'critico') {
                $query->whereColumn('stock', '<=', 'stock_critico');
            } elseif ($request->alerta === 'bajo') {
                $query->whereColumn('stock', '<=', 'stock_minimo')
                      ->whereColumn('stock', '>', 'stock_critico');
            } elseif ($request->alerta === 'sin_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        $productos = $query->paginate(10)->withQueryString();
        $categorias = Categoria::orderBy('nombre')->get();

        // Métricas de inventario para las tarjetas superiores
        $totalProductos = Producto::count();
        $stockBajo = Producto::whereColumn('stock', '<=', 'stock_minimo')
                             ->whereColumn('stock', '>', 'stock_critico')
                             ->count();
        $stockCritico = Producto::whereColumn('stock', '<=', 'stock_critico')->count();
        $valorInventario = Producto::selectRaw('COALESCE(SUM(stock * precio_compra), 0) as total')->value('total') ?? 0;

        return view('productos.index', compact(
            'productos',
            'categorias',
            'totalProductos',
            'stockBajo',
            'stockCritico',
            'valorInventario'
        ));
    }

    // GET /productos/create → muestra el formulario de carga (Solo Admin)
    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede agregar productos.');
        }

        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('empresa')->get();

        return view('productos.create', compact('categorias', 'proveedores'));
    }

    // POST /productos → guarda el nuevo producto (Solo Admin)
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede agregar productos.');
        }

        $validated = $this->validarProducto($request);

        DB::transaction(function () use ($validated) {
            $producto = Producto::create($validated);

            // Si se cargó con stock inicial, registrar movimiento de entrada
            if ($producto->stock > 0) {
                MovimientoStock::create([
                    'id_producto'    => $producto->id_producto,
                    'id_user'        => Auth::id(),
                    'tipo'           => 'entrada',
                    'cantidad'       => $producto->stock,
                    'stock_anterior' => 0,
                    'stock_nuevo'    => $producto->stock,
                    'motivo'         => 'Carga de stock inicial',
                ]);
            }
        });

        return redirect()->route('productos.index')
                         ->with('success', 'Producto creado correctamente.');
    }

    // GET /productos/{producto}/edit → muestra el formulario con los datos cargados (Solo Admin)
    public function edit(Producto $producto)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede editar productos.');
        }

        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('empresa')->get();

        return view('productos.edit', compact('producto', 'categorias', 'proveedores'));
    }

    // PUT /productos/{producto} → actualiza el producto existente (Solo Admin)
    public function update(Request $request, Producto $producto)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede editar productos.');
        }

        $validated = $this->validarProducto($request, $producto->id_producto);

        $stockAnterior = $producto->stock;
        $stockNuevo = (int) $validated['stock'];

        DB::transaction(function () use ($producto, $validated, $stockAnterior, $stockNuevo) {
            $producto->update($validated);

            // Si cambió el stock directamente en la edición, registrar la auditoría
            if ($stockAnterior !== $stockNuevo) {
                MovimientoStock::create([
                    'id_producto'    => $producto->id_producto,
                    'id_user'        => Auth::id(),
                    'tipo'           => 'ajuste_manual',
                    'cantidad'       => abs($stockNuevo - $stockAnterior),
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo'    => $stockNuevo,
                    'motivo'         => 'Modificación de stock desde edición de ficha',
                ]);
            }
        });

        return redirect()->route('productos.index')
                         ->with('success', 'Producto actualizado correctamente.');
    }

    // DELETE /productos/{producto} → elimina el producto con validación de relaciones (Solo Admin)
    public function destroy(Producto $producto)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un administrador puede eliminar productos.');
        }

        // Regla de integridad: No eliminar si tiene ventas o compras históricas
        if ($producto->tieneMovimientos()) {
            return back()->with('error', "No se puede eliminar '{$producto->nombre}' porque posee ventas o recepciones históricas registradas. Si ya no se comercializa, ajusta su stock a 0.");
        }

        $nombre = $producto->nombre;
        $producto->delete();

        return redirect()->route('productos.index')
                         ->with('success', "Producto '{$nombre}' eliminado correctamente.");
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
            'precio_compra'  => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'precio_venta'   => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/', 'gte:precio_compra'],
            'stock'          => 'required|integer|min:0',
            'stock_minimo'   => 'required|integer|min:0',
            'stock_critico'  => 'required|integer|min:0|lte:stock_minimo',
            'id_categoria'   => 'nullable|exists:categorias,id_categoria',
            'id_proveedor'   => 'nullable|exists:proveedores,id_proveedor',
        ], [
            'precio_venta.gte'    => 'El precio de venta no puede ser menor al costo de compra.',
            'precio_compra.gt'    => 'El costo de compra debe ser mayor a $0.',
            'precio_venta.gt'     => 'El precio de venta debe ser mayor a $0.',
            'precio_compra.regex' => 'Formato inválido en costo de compra (máximo 2 decimales).',
            'precio_venta.regex'  => 'Formato inválido en precio de venta (máximo 2 decimales).',
            'stock_critico.lte'   => 'El stock crítico no puede ser mayor al stock mínimo.',
        ]);
    }
}