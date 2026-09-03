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
        'id_categoria',
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
        // belongsTo(Modelo, clave_foránea_en_esta_tabla, clave_en_la_tabla_relacionada)
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
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