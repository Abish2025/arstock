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

    // Le decimos a Eloquent que trate estos campos como decimales de 2 dígitos,
    // para que no aparezcan como texto o con decimales raros al mostrarlos
    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta'  => 'decimal:2',
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

    // Este es un "accessor": crea un campo virtual $producto->estado_stock
    // que no existe en la base de datos, se calcula al vuelo
    public function getEstadoStockAttribute(): string
    {
        if ($this->stock <= $this->stock_critico) {
            return 'critico'; // Necesita reposición urgente
        }
        if ($this->stock <= $this->stock_minimo) {
            return 'bajo'; // Se está por acabar
        }
        return 'normal'; // Stock saludable
    }
}