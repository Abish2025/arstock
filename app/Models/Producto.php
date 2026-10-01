<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'nombre', 'codigo', 'descripcion',
        'precio_compra', 'precio_venta',
        'stock', 'stock_minimo', 'stock_critico',
        'id_categoria', 'id_proveedor',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta'  => 'decimal:2',
        'stock'         => 'integer',
        'stock_minimo'  => 'integer',
        'stock_critico' => 'integer',
    ];

    // Relación: un producto pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    // Relación: un producto es provisto por un proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    // Relación: movimientos o ajustes de inventario
    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class, 'id_producto', 'id_producto')->latest();
    }

    // Relación: ventas en las que participó el producto
    public function detallesVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }

    // Relación: compras / ingresos de mercadería
    public function detallesCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }

    public function getEstadoStockAttribute(): string
    {
        if ($this->stock <= $this->stock_critico) {
            return 'critico';
        }
        if ($this->stock <= $this->stock_minimo) {
            return 'bajo';
        }
        return 'normal';
    }

    public function tieneMovimientos(): bool
    {
        return $this->detallesVentas()->exists() || $this->detallesCompras()->exists();
    }
}