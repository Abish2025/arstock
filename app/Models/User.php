<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'activo',
        'telefono',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function isCajero(): bool
    {
        return $this->rol === 'cajero';
    }

    public function cajas()
    {
        return $this->hasMany(Caja::class, 'id_user');
    }

    public function cajaAbierta()
    {
        return $this->hasOne(Caja::class, 'id_user')->where('estado', 'abierta')->latest('fecha_apertura');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_user');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_user');
    }
}
