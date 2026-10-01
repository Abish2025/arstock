<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Si la cuenta fue desactivada, desloguear y redirigir
        if (!$user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta ha sido desactivada por el administrador.',
            ]);
        }

        // Si se especificaron roles permitidos, verificar que el usuario tenga alguno de ellos
        if (!empty($roles) && !in_array($user->rol, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para realizar esta acción.'], 403);
            }
            abort(403, 'Acceso denegado: tu rol no tiene los permisos necesarios para acceder a este módulo.');
        }

        return $next($request);
    }
}
