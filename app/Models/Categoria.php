<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // Le decimos a Eloquent que la clave primaria no se llama "id" sino "id_categoria"
    protected $primaryKey = 'id_categoria';

    // Campos que se pueden asignar masivamente (con create() o update())
    // Es una medida de seguridad de Laravel contra "mass assignment"
    protected $fillable = ['nombre'];

    // Relación: una categoría tiene muchos productos
    public function productos()
    {
        // hasMany(Modelo, clave_foránea_en_la_otra_tabla, clave_local)
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }
}