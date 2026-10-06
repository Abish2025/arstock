<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\Auth\LoginController;

// ─── Rutas Públicas (Sin Sesión) ───────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Verificación de correo ───────────────────────────────────────────────

// Página que aparece cuando el usuario todavía no verificó su correo
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


// Enlace que llega por correo
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    // Buscar al usuario al que pertenece el enlace
    $user = \App\Models\User::findOrFail($id);

    // Comprobar que el enlace firmado sea válido
    if (!$request->hasValidSignature()) {
        abort(403, 'El enlace de verificación no es válido o ha expirado.');
    }

    // Comprobar que el hash corresponda al correo del usuario
    if (!hash_equals(
        (string) $hash,
        sha1($user->getEmailForVerification())
    )) {
        abort(403, 'El enlace de verificación no corresponde a este usuario.');
    }

    // Si ya estaba verificado
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('login')
            ->with('success', 'Este correo ya estaba verificado.');
    }

    // Marcar el correo como verificado
    $user->markEmailAsVerified();

   return view('auth.email-verified');

})->middleware('signed')->name('verification.verify');


// Reenviar correo de verificación
Route::post('/email/verification-notification', function (Request $request) {

    $request->user()->sendEmailVerificationNotification();

    return back()->with(
        'success',
        'Te enviamos un nuevo correo de verificación.'
    );

})->middleware(['auth', 'throttle:6,1'])
  ->name('verification.send');


// ─── Rutas Protegidas (Requieren Login y Usuario Activo) ───────────────────
Route::middleware(['auth', 'verified', 'role'])->group(function () {

    // Dashboard Principal (Adaptado al rol de Admin o Cajero)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ─── Módulo de Cajas y Turnos ──────────────────────────────────────────
    Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index');
    Route::post('cajas/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
    Route::get('cajas/{caja}', [CajaController::class, 'show'])->name('cajas.show');
    Route::post('cajas/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');

    // ─── Módulo de Ventas y Punto de Cobro (POS) ───────────────────────────
    Route::resource('ventas', VentaController::class)
         ->parameters(['ventas' => 'venta'])
         ->only(['index', 'create', 'store', 'show']);

    // Anulación de Venta (Restringida a Administradores)
    Route::delete('ventas/{venta}', [VentaController::class, 'destroy'])
         ->middleware('role:admin')
         ->name('ventas.destroy');

    // ─── Módulo de Clientes y Cuentas Corrientes ────────────────────────────
    Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::post('clientes/{cliente}/movimiento', [ClienteController::class, 'registrarMovimiento'])->name('clientes.movimiento');

    // Edición y Eliminación de Clientes (Solo Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    });

    // ─── Módulo de Productos (Catálogo) ────────────────────────────────────
    Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');

    // ABM de Productos (Solo Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });

    // ─── Módulo de Auditoría de Stock y Ajustes ─────────────────────────────
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::middleware('role:admin')->group(function () {
        Route::get('stock/ajuste', [StockController::class, 'createAjuste'])->name('stock.ajuste');
        Route::post('stock/ajuste', [StockController::class, 'storeAjuste'])->name('stock.ajuste.store');
    });

    // ─── Módulos Exclusivos de Administrador ─────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Proveedores
        Route::resource('proveedores', ProveedorController::class)
             ->parameters(['proveedores' => 'proveedor']);

        // Ingreso de Mercadería y Compras
        Route::resource('compras', CompraController::class)
             ->parameters(['compras' => 'compra'])
             ->only(['index', 'create', 'store', 'show']);

        // Reportes y Rentabilidad
        Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/reposicion-imprimir', [ReporteController::class, 'imprimirReposicion'])->name('reportes.reposicion.imprimir');

        // Gestión de Usuarios y Empleados
        Route::resource('usuarios', UserController::class)
             ->parameters(['usuarios' => 'usuario']);
        Route::post('usuarios/{usuario}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
        Route::post('usuarios/{usuario}/reset-password', [UserController::class, 'resetPassword'])->name('usuarios.reset-password');
    });

});