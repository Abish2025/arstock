<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'cajas';
    protected $primaryKey = 'id_caja';

    protected $fillable = [
        'id_user',
        'monto_apertura',
        'monto_cierre_esperado',
        'monto_cierre_real',
        'diferencia',
        'fecha_apertura',
        'fecha_cierre',
        'estado',
        'notas_apertura',
        'notas_cierre',
    ];

    protected $casts = [
        'monto_apertura' => 'decimal:2',
        'monto_cierre_esperado' => 'decimal:2',
        'monto_cierre_real' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_caja', 'id_caja');
    }

    public function getEstaAbiertaAttribute(): bool
    {
        return $this->estado === 'abierta';
    }
}
