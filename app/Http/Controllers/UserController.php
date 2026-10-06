<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // GET /usuarios
    public function index(Request $request)
    {
        $query = User::orderBy('name');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('name', 'ilike', "%{$buscar}%")
                  ->orWhere('email', 'ilike', "%{$buscar}%")
                  ->orWhere('telefono', 'ilike', "%{$buscar}%");
            });
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        $usuarios = $query->paginate(10)->withQueryString();

        $totalUsuarios = User::count();
        $totalAdmins = User::where('rol', 'admin')->count();
        $totalCajeros = User::where('rol', 'cajero')->count();
        $totalActivos = User::where('activo', true)->count();

        return view('usuarios.index', compact(
            'usuarios',
            'totalUsuarios',
            'totalAdmins',
            'totalCajeros',
            'totalActivos'
        ));
    }

    // GET /usuarios/create
    public function create()
    {
        return view('usuarios.create');
    }

    // POST /usuarios
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|min:2|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'rol'      => 'required|in:admin,cajero',
            'telefono' => 'nullable|string|max:30',
            'activo'   => 'nullable|boolean',
        ], [
            'email.unique'       => 'Ya existe un usuario con este correo electrónico.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['activo']   = $request->has('activo');

        $user = User::create($validated);

        $user->sendEmailVerificationNotification();

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario {$validated['name']} creado exitosamente. Se envió un correo de verificación a {$validated['email']}.");
    }

    // GET /usuarios/{user}/edit
    public function edit(User $usuario)
    {
        return view('usuarios.edit', ['user' => $usuario]);
    }

    // PUT /usuarios/{user}
    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'     => 'required|string|min:2|max:100',
            'email'    => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'rol'      => 'required|in:admin,cajero',
            'telefono' => 'nullable|string|max:30',
            'activo'   => 'nullable|boolean',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'email.unique'       => 'Ya existe otro usuario con este correo electrónico.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // Evitar que el administrador se quite el rol de admin o se desactive a sí mismo
        if ($usuario->id === Auth::id()) {
            if ($validated['rol'] !== 'admin') {
                return back()->withInput()->with('error', 'No puedes quitarte el rol de Administrador a ti mismo.');
            }
            $validated['activo'] = true;
        } else {
            $validated['activo'] = $request->has('activo');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.index')
                         ->with('success', "Datos de {$usuario->name} actualizados.");
    }

    // POST /usuarios/{user}/toggle-status
    public function toggleStatus(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta de usuario.');
        }

        $usuario->update(['activo' => !$usuario->activo]);

        $estado = $usuario->activo ? 'activado' : 'desactivado';
        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario {$usuario->name} {$estado} correctamente.");
    }

    // POST /usuarios/{user}/reset-password
    public function resetPassword(Request $request, User $usuario)
    {
        $request->validate([
            'nueva_password' => 'required|string|min:6|confirmed',
        ], [
            'nueva_password.confirmed' => 'Las contraseñas no coinciden.',
            'nueva_password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $usuario->update([
            'password' => Hash::make($request->nueva_password),
        ]);

        return redirect()->route('usuarios.index')
                         ->with('success', "Contraseña de {$usuario->name} restablecida exitosamente.");
    }

    // DELETE /usuarios/{user}
    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta de usuario.');
        }

        // Verificar si tiene ventas o turnos asociados para no perder trazabilidad
        if ($usuario->ventas()->exists() || $usuario->cajas()->exists() || $usuario->compras()->exists()) {
            return back()->with('error', "No se puede eliminar a {$usuario->name} porque tiene operaciones registradas (ventas, cajas o compras). Puedes desactivar su cuenta en su lugar.");
        }

        $nombre = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario {$nombre} eliminado correctamente.");
    }
}
