<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class FlaskMicroserviceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Crea los roles y obtén su ID explícitamente
        $userRole = Role::create([
            'id' => 2,
            'label' => 'user',
            'name' => 'user'
        ]);

        // Ahora crea el usuario con el ID del rol ya confirmado en BD
        $this->user = User::factory()->create([
            'role_id' => $userRole->id,
        ]);

        $this->token = JWTAuth::fromUser($this->user);
    }

    /** @test */
    public function flask_service_returns_predictions_with_valid_credentials()
    {
        // Mock de la respuesta del microservicio Flask
        Http::fake([
            env('MICROSERVICE_FLASK_URL').'/obtener_predicciones' => Http::response([
                ['id' => '1', 'texto' => 'Ejemplo', 'sentimiento' => 'Neutral']
            ], 200, ['x-api-key' => env('MICROSERVICE_FLASK_KEY')])
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/comments');

        $this->assertNotNull($this->token); // Asegúrate que se generó el token
        $this->assertAuthenticatedAs($this->user); // Laravel reconoce al usuario en sesión


        $response->assertStatus(200)
            ->assertJsonStructure([['id', 'texto', 'sentimiento']]);
    }

    /** @test */
    public function flask_service_blocks_unauthenticated_requests()
    {
        $response = $this->getJson('/api/comments');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
public function envia_correo_si_sentimiento_es_negativo()
{
    Http::fake([
        env('MICROSERVICE_FLASK_URL') . '/predecir' => Http::response([
            'sentimiento' => 'Negativo'
        ], 200),

        env('MICROSERVICE_EMAIL_URL') => Http::response([
            'mensaje' => 'Correo enviado correctamente'
        ], 200),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/comments', [
        'texto' => 'Estoy triste y frustrado con este servicio.'
    ]);

    $response->assertStatus(200);
    $this->assertEquals('Negativo', $response['sentimiento']);
}

/** @test */
public function no_envia_correo_si_sentimiento_no_es_negativo()
{
    Http::fake([
        env('MICROSERVICE_FLASK_URL') . '/predecir' => Http::response([
            'sentimiento' => 'Positivo'
        ], 200),

        env('MICROSERVICE_EMAIL_URL') => Http::response(null, 500),
    ]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/comments', [
        'texto' => 'Hoy es un buen día'
    ]);

    $response->assertStatus(200);
    $this->assertEquals('Positivo', $response['sentimiento']);
}
}