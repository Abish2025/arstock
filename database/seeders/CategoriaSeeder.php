<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        // create() inserta un registro nuevo usando los campos definidos en $fillable
        Categoria::create(['nombre' => 'Informática']);
        Categoria::create(['nombre' => 'Periféricos']);
        Categoria::create(['nombre' => 'Accesorios']);
        
    }
}