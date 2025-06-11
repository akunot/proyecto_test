<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['id' => 1, 'label' => 'admin', 'name' => 'admin']);
        Role::create(['id' => 2, 'label' => 'user', 'name' => 'user']);
    }

    /** @test */
    public function gateway_allows_valid_user_registration()
    {
        // Primero crea un rol con todos los campos requeridos
        $role = Role::create([
            'name' => 'user',
            'label' => 'usuario', // Campo requerido
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $role->id
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function gateway_blocks_invalid_registration_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'invalid',
            'password' => 'short',
            'password_confirmation' => 'mismatch'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
