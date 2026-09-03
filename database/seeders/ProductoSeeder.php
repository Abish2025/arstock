<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // id_categoria hace referencia al orden en que se crearon en CategoriaSeeder
        // (1 = Informática, 2 = Periféricos, 3 = Accesorios)
        Producto::create([
            'nombre' => 'Notebook', 'codigo' => 'NB-001',
            'descripcion' => 'Notebook 15 pulgadas',
            'precio_compra' => 700000, 'precio_venta' => 850000,
            'stock' => 10, 'stock_minimo' => 5, 'stock_critico' => 2,
            'id_categoria' => 1,
        ]);
        Producto::create([
            'nombre' => 'Mouse', 'codigo' => 'MO-001',
            'descripcion' => 'Mouse óptico USB',
            'precio_compra' => 8000, 'precio_venta' => 12000,
            'stock' => 50, 'stock_minimo' => 15, 'stock_critico' => 5,
            'id_categoria' => 2,
        ]);
        Producto::create([
            'nombre' => 'Teclado', 'codigo' => 'TE-001',
            'descripcion' => 'Teclado mecánico',
            'precio_compra' => 18000, 'precio_venta' => 25000,
            'stock' => 3, 'stock_minimo' => 10, 'stock_critico' => 4,
            'id_categoria' => 2,
        ]);
    }
}