<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\NotificacionEmail;

class EmailServiceTest extends TestCase
{
    /** @test */
    public function envia_correo_correctamente_con_datos_validos()
    {
        Mail::fake();

        $response = $this->postJson('/api/enviar-correo', [
            'destinatario' => 'usuario@correo.com',
            'asunto' => 'Prueba de envío',
            'titulo' => 'Título de prueba',
            'mensaje' => 'Este es el cuerpo del correo.'
        ]);

        $response->assertStatus(200)
                ->assertJson(['mensaje' => 'Correo enviado correctamente']);

        Mail::assertSent(\App\Mail\NotificacionEmail::class, function ($mail) {
            return $mail->hasTo('usuario@correo.com') &&
                $mail->datos['asunto'] === 'Prueba de envío' &&
                $mail->datos['titulo'] === 'Título de prueba' &&
                $mail->datos['mensaje'] === 'Este es el cuerpo del correo.';
        });
    }

    /** @test */
    public function no_envia_correo_si_faltan_datos_requeridos()
    {
        Mail::fake();

        $response = $this->postJson('/api/enviar-correo', [
            'destinatario' => 'usuario@correo.com',
            // Falta 'asunto', 'titulo', 'mensaje'
        ]);

        $response->assertStatus(422); // Laravel responde con 422 por validación fallida
        Mail::assertNothingSent();
    }
}
