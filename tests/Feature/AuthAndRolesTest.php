<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRolesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_con_credenciales_correctas(): void
    {
        $user = User::factory()->create([
            'email'    => 'test_login@arstock.com',
            'password' => Hash::make('password123'),
            'rol'      => 'admin',
            'activo'   => true,
        ]);

        $response = $this->post('/login', [
            'email'    => 'test_login@arstock.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_con_credenciales_incorrectas(): void
    {
        $response = $this->post('/login', [
            'email'    => 'noexiste@arstock.com',
            'password' => 'claveerronea',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_usuario_desactivado_no_puede_iniciar_sesion(): void
    {
        $user = User::factory()->create([
            'email'    => 'desactivado@arstock.com',
            'password' => Hash::make('password123'),
            'rol'      => 'cajero',
            'activo'   => false,
        ]);

        $response = $this->post('/login', [
            'email'    => 'desactivado@arstock.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_puede_acceder_a_usuarios_y_reportes(): void
    {
        $admin = User::where('rol', 'admin')->where('activo', true)->first();

        $this->actingAs($admin)->get('/usuarios')->assertStatus(200);
        $this->actingAs($admin)->get('/reportes')->assertStatus(200);
    }

    public function test_cajero_no_puede_acceder_a_usuarios_ni_reportes(): void
    {
        $cajero = User::where('rol', 'cajero')->where('activo', true)->first()
            ?? User::factory()->create(['rol' => 'cajero', 'activo' => true]);

        // Debe retornar 403 Prohibido
        $this->actingAs($cajero)->get('/usuarios')->assertStatus(403);
        $this->actingAs($cajero)->get('/reportes')->assertStatus(403);
    }

    public function test_cajero_puede_acceder_al_pos_y_cajas(): void
    {
        $cajero = User::where('rol', 'cajero')->where('activo', true)->first()
            ?? User::factory()->create(['rol' => 'cajero', 'activo' => true]);

        $this->actingAs($cajero)->get('/ventas/create')->assertStatus(200);
        $this->actingAs($cajero)->get('/cajas')->assertStatus(200);
    }
}
