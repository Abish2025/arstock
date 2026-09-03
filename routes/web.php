<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

// Redirige la raíz del sitio directo a la lista de productos
Route::get('/', function () {
    return redirect()->route('productos.index');
});

// Route::resource genera automáticamente las 5 rutas del ABM:
// GET /productos            → index
// GET /productos/create     → create
// POST /productos           → store
// GET /productos/{id}/edit  → edit
// PUT /productos/{id}       → update
// DELETE /productos/{id}    → destroy
//
// parameters() cambia el nombre del parámetro de {producto} en vez de {producto} genérico
// (esto es solo para que las rutas se vean más prolijas, ej: /productos/5/edit usa $producto en el controlador)
Route::resource('productos', ProductoController::class)
     ->parameters(['productos' => 'producto'])
     ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);