<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $primaryKey = 'id_venta';

    protected $fillable = [
        'codigo',
        'id_user',
        'id_caja',
        'id_cliente',
        'cliente_nombre',
        'metodo_pago',
        'total',
        'monto_recibido',
        'vuelto',
        'referencia_pago',
        'estado',
        'notas',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'vuelto' => 'decimal:2',
    ];

    // Relación con el usuario/cajero que registró la venta
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relación con el turno/caja en el que se realizó la venta
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id_caja');
    }

    // Relación con el cliente (si no fue Consumidor Final)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    // Relación con los renglones / productos comprados
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    // Helper: Resumen de productos en texto para la tabla (como en Figma)
    public function getResumenProductosAttribute(): string
    {
        $nombres = $this->detalles->pluck('producto_nombre')->take(2);
        $totalItems = $this->detalles->count();

        if ($nombres->isEmpty()) {
            return 'Varios productos';
        }

        $resumen = $nombres->join(', ');
        if ($totalItems > 2) {
            $resumen .= '... (+ ' . ($totalItems - 2) . ')';
        }

        return $resumen;
    }

    // Helper: Saber si la venta fue fiada
    public function getEsFiadoAttribute(): bool
    {
        return $this->metodo_pago === 'fiado';
    }

    public function getEstaAnuladaAttribute(): bool
    {
        return $this->estado === 'anulada';
    }
}
