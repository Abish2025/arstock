<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCuentaCorriente extends Model
{
    protected $table = 'movimientos_cuenta_corriente';
    protected $primaryKey = 'id_movimiento';

    protected $fillable = [
        'id_cliente',
        'tipo',
        'monto',
        'concepto',
        'saldo_anterior',
        'saldo_nuevo',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_nuevo' => 'decimal:2',
    ];

    // Relación: Un movimiento pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }
}
