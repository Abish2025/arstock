<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;

// Dashboard Principal (Pantalla de inicio)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Rutas de Productos (Inventario)
Route::resource('productos', ProductoController::class)
     ->parameters(['productos' => 'producto'])
     ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

// Rutas de Clientes y Fiados (Cuentas Corrientes)
Route::resource('clientes', ClienteController::class)
     ->parameters(['clientes' => 'cliente']);
Route::post('clientes/{cliente}/movimiento', [ClienteController::class, 'registrarMovimiento'])
     ->name('clientes.movimiento');