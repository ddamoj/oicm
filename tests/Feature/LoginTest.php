<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_correcto_redirige_al_panel(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Control2026')]);

        $respuesta = $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'Oicm#Control2026',
        ]);

        $this->assertAuthenticatedAs($usuario);
        $respuesta->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_credenciales_invalidas_no_autentican(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Control2026')]);

        $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'contrasena-incorrecta',
        ]);

        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $usuario = User::factory()->administrador()->create([
            'password' => bcrypt('Oicm#Control2026'),
            'activo' => false,
        ]);

        $respuesta = $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'Oicm#Control2026',
        ]);

        $this->assertGuest();
        $respuesta->assertSessionHasErrors('email');
    }

    public function test_usuario_eliminado_no_puede_iniciar_sesion(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Control2026')]);
        $usuario->delete();

        $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'Oicm#Control2026',
        ]);

        $this->assertGuest();
    }

    public function test_tras_varios_intentos_fallidos_se_aplica_bloqueo_temporal(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Control2026')]);

        for ($intento = 0; $intento < 5; $intento++) {
            $this->post('/login', [
                'email' => $usuario->email,
                'password' => 'contrasena-incorrecta',
            ]);
        }

        $respuesta = $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'contrasena-incorrecta',
        ]);

        $respuesta->assertStatus(429);
        $this->assertGuest();
    }

    public function test_la_ruta_de_registro_ya_no_existe(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }
}
