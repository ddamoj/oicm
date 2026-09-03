<?php

namespace Tests\Feature;

use App\Livewire\Admin\Usuarios\FormularioUsuario;
use App\Models\BitacoraAuditoria;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BitacoraAuditoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_exitoso_deja_registro_en_la_bitacora(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Control2026')]);

        $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'Oicm#Control2026',
        ]);

        $this->assertDatabaseHas('bitacora_auditoria', [
            'accion' => 'login_exitoso',
            'user_id' => $usuario->id,
            'modelo_afectado' => 'User',
            'modelo_id' => $usuario->id,
        ]);

        $registro = BitacoraAuditoria::query()->where('accion', 'login_exitoso')->firstOrFail();
        $this->assertNotNull($registro->ip);
        $this->assertNotNull($registro->creado_en);
    }

    public function test_login_fallido_deja_registro_en_la_bitacora(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Control2026')]);

        $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'contrasena-incorrecta',
        ]);

        $this->assertDatabaseHas('bitacora_auditoria', [
            'accion' => 'login_fallido',
            'modelo_afectado' => 'User',
            'modelo_id' => $usuario->id,
        ]);
    }

    public function test_alta_y_baja_de_usuario_dejan_registro_en_la_bitacora(): void
    {
        $administrador = User::factory()->administrador()->create();
        $rolContenido = Rol::firstOrCreate(['clave' => 'administrador_contenido'], ['nombre' => 'Administrador de Contenido']);

        Livewire::actingAs($administrador)
            ->test(FormularioUsuario::class)
            ->set('name', 'Persona Auditada')
            ->set('email', 'auditada@oicm.oaxacadejuarez.gob.mx')
            ->set('rolId', (string) $rolContenido->id)
            ->set('password', 'Oicm#Auditada2026')
            ->set('password_confirmation', 'Oicm#Auditada2026')
            ->call('guardar');

        $nuevoUsuario = User::query()->where('email', 'auditada@oicm.oaxacadejuarez.gob.mx')->firstOrFail();

        $this->assertDatabaseHas('bitacora_auditoria', [
            'accion' => 'usuario_creado',
            'user_id' => $administrador->id,
            'modelo_afectado' => 'User',
            'modelo_id' => $nuevoUsuario->id,
        ]);
    }
}
