<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'saldo',
        'limite_credito',
        'notas',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'limite_credito' => 'decimal:2',
    ];

    // Relación: Un cliente tiene muchos movimientos de fiados y pagos
    public function movimientos()
    {
        return $this->hasMany(MovimientoCuentaCorriente::class, 'id_cliente', 'id_cliente')
                    ->orderByDesc('created_at');
    }

    // Helper: Saber si el cliente debe dinero
    public function getTieneDeudaAttribute(): bool
    {
        return $this->saldo > 0;
    }

    // Helper: Estado del cliente para colores y badges
    public function getEstadoDeudaAttribute(): string
    {
        if ($this->saldo <= 0) {
            return 'al_dia';
        }

        if ($this->limite_credito && $this->saldo > $this->limite_credito) {
            return 'limite_excedido';
        }

        return 'con_deuda';
    }
}
