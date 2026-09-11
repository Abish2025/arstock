<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';

    protected $fillable = [
        'empresa',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'cuit',
        'dias_visita',
        'notas',
    ];

    // Relación: Un proveedor provee muchos productos al inventario
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_proveedor', 'id_proveedor');
    }
}
