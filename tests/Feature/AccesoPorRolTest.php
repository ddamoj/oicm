<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesoPorRolTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_autenticado_es_redirigido_al_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/admin/usuarios')->assertRedirect(route('login'));
    }

    public function test_administrador_de_contenido_no_accede_a_usuarios_ni_bitacora(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        $this->actingAs($usuario)->get('/admin/usuarios')->assertForbidden();
        $this->actingAs($usuario)->get('/admin/bitacora')->assertForbidden();
    }

    public function test_administrador_accede_a_usuarios_y_bitacora(): void
    {
        $usuario = User::factory()->administrador()->create();

        $this->actingAs($usuario)->get('/admin/usuarios')->assertOk();
        $this->actingAs($usuario)->get('/admin/bitacora')->assertOk();
    }

    public function test_menu_lateral_no_muestra_usuarios_al_administrador_de_contenido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        $respuesta = $this->actingAs($usuario)->get('/admin');

        $respuesta->assertOk();
        $respuesta->assertDontSee('Usuarios');
        $respuesta->assertDontSee('Bitácora');
    }

    public function test_menu_lateral_muestra_usuarios_al_administrador(): void
    {
        $usuario = User::factory()->administrador()->create();

        $respuesta = $this->actingAs($usuario)->get('/admin');

        $respuesta->assertOk();
        $respuesta->assertSee('Usuarios');
        $respuesta->assertSee('Bitácora');
    }
}
