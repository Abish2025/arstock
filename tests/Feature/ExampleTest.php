<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Un usuario no autenticado debe ser redirigido al login.
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    /**
     * Un usuario autenticado accede exitosamente al dashboard.
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('rol', 'admin')->first() ?? User::factory()->create(['rol' => 'admin', 'activo' => true]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }
}
