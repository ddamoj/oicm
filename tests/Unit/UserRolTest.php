<?php

namespace Tests\Unit;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_pertenece_a_un_rol(): void
    {
        $rol = Rol::factory()->create(['clave' => 'administrador']);
        $usuario = User::factory()->create(['rol_id' => $rol->id]);

        $this->assertTrue($usuario->rol->is($rol));
    }

    public function test_tiene_rol_es_null_safe_cuando_el_usuario_no_tiene_rol(): void
    {
        $usuario = User::factory()->create(['rol_id' => null]);

        $this->assertFalse($usuario->tieneRol('administrador'));
    }

    public function test_tiene_rol_identifica_correctamente_la_clave_del_rol(): void
    {
        $rol = Rol::factory()->create(['clave' => 'administrador_contenido']);
        $usuario = User::factory()->create(['rol_id' => $rol->id]);

        $this->assertTrue($usuario->tieneRol('administrador_contenido'));
        $this->assertFalse($usuario->tieneRol('administrador'));
    }
}
