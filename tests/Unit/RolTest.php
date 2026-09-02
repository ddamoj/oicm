<?php

namespace Tests\Unit;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_rol_tiene_muchos_usuarios(): void
    {
        $rol = Rol::factory()->create(['clave' => 'administrador']);
        $usuario = User::factory()->create(['rol_id' => $rol->id]);

        $this->assertTrue($rol->usuarios->contains($usuario));
    }

    public function test_la_clave_del_rol_es_unica(): void
    {
        Rol::factory()->create(['clave' => 'administrador']);

        $this->expectException(QueryException::class);

        Rol::factory()->create(['clave' => 'administrador']);
    }
}
