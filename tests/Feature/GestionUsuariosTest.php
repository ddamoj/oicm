<?php

namespace Tests\Feature;

use App\Livewire\Admin\Usuarios\FormularioUsuario;
use App\Livewire\Admin\Usuarios\ListaUsuarios;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class GestionUsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_puede_dar_de_alta_un_usuario(): void
    {
        $administrador = User::factory()->administrador()->create();
        $rolContenido = Rol::firstOrCreate(['clave' => 'administrador_contenido'], ['nombre' => 'Administrador de Contenido']);

        Livewire::actingAs($administrador)
            ->test(FormularioUsuario::class)
            ->set('name', 'Nueva Persona')
            ->set('email', 'nueva.persona@oicm.oaxacadejuarez.gob.mx')
            ->set('rolId', (string) $rolContenido->id)
            ->set('password', 'Oicm#Nueva2026')
            ->set('password_confirmation', 'Oicm#Nueva2026')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'nueva.persona@oicm.oaxacadejuarez.gob.mx',
            'rol_id' => $rolContenido->id,
        ]);
    }

    public function test_no_permite_correo_duplicado(): void
    {
        $administrador = User::factory()->administrador()->create();
        $existente = User::factory()->administradorContenido()->create();

        Livewire::actingAs($administrador)
            ->test(FormularioUsuario::class)
            ->set('name', 'Otra Persona')
            ->set('email', $existente->email)
            ->set('rolId', (string) $existente->rol_id)
            ->set('password', 'Oicm#Nueva2026')
            ->set('password_confirmation', 'Oicm#Nueva2026')
            ->call('guardar')
            ->assertHasErrors(['email']);
    }

    public function test_rechaza_contrasena_debil(): void
    {
        $administrador = User::factory()->administrador()->create();
        $rolContenido = Rol::firstOrCreate(['clave' => 'administrador_contenido'], ['nombre' => 'Administrador de Contenido']);

        Livewire::actingAs($administrador)
            ->test(FormularioUsuario::class)
            ->set('name', 'Nueva Persona')
            ->set('email', 'debil@oicm.oaxacadejuarez.gob.mx')
            ->set('rolId', (string) $rolContenido->id)
            ->set('password', 'abc123')
            ->set('password_confirmation', 'abc123')
            ->call('guardar')
            ->assertHasErrors(['password']);
    }

    public function test_administrador_puede_editar_el_rol_de_otro_usuario(): void
    {
        $administrador = User::factory()->administrador()->create();
        $objetivo = User::factory()->administradorContenido()->create();
        $rolAdministrador = Rol::where('clave', 'administrador')->first();

        Livewire::actingAs($administrador)
            ->test(FormularioUsuario::class)
            ->call('prepararEdicion', $objetivo->id)
            ->set('rolId', (string) $rolAdministrador->id)
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertSame($rolAdministrador->id, $objetivo->fresh()->rol_id);
    }

    public function test_desactivar_revoca_el_acceso_de_inmediato(): void
    {
        $administrador = User::factory()->administrador()->create();
        $objetivo = User::factory()->administradorContenido()->create(['password' => bcrypt('Oicm#Objetivo2026')]);

        // Simula una sesión activa del usuario objetivo (driver de sesión "database").
        DB::table('sessions')->insert([
            'id' => 'sesion-de-prueba',
            'user_id' => $objetivo->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
            'payload' => base64_encode('datos'),
            'last_activity' => time(),
        ]);

        Livewire::actingAs($administrador)
            ->test(ListaUsuarios::class)
            ->call('desactivar', $objetivo->id);

        $this->assertFalse($objetivo->fresh()->activo);
        // La sesión abierta se elimina en el acto: revocación inmediata (RF-USR-001).
        $this->assertDatabaseMissing('sessions', ['user_id' => $objetivo->id]);
    }

    public function test_eliminar_hace_baja_suave(): void
    {
        $administrador = User::factory()->administrador()->create();
        $objetivo = User::factory()->administradorContenido()->create();

        Livewire::actingAs($administrador)
            ->test(ListaUsuarios::class)
            ->call('eliminar', $objetivo->id);

        $this->assertSoftDeleted('users', ['id' => $objetivo->id]);
    }

    public function test_no_puedo_desactivarme_a_mi_mismo(): void
    {
        $administrador = User::factory()->administrador()->create();

        Livewire::actingAs($administrador)
            ->test(ListaUsuarios::class)
            ->call('desactivar', $administrador->id);

        $this->assertTrue($administrador->fresh()->activo);
    }

    public function test_no_puedo_eliminarme_a_mi_mismo(): void
    {
        $administrador = User::factory()->administrador()->create();

        Livewire::actingAs($administrador)
            ->test(ListaUsuarios::class)
            ->call('eliminar', $administrador->id);

        $this->assertNotSoftDeleted('users', ['id' => $administrador->id]);
    }

    public function test_no_se_puede_dejar_el_sistema_sin_administradores_activos(): void
    {
        $unicoAdministrador = User::factory()->administrador()->create();
        $rolContenido = Rol::firstOrCreate(['clave' => 'administrador_contenido'], ['nombre' => 'Administrador de Contenido']);

        Livewire::actingAs($unicoAdministrador)
            ->test(FormularioUsuario::class)
            ->call('prepararEdicion', $unicoAdministrador->id)
            ->set('rolId', (string) $rolContenido->id)
            ->call('guardar')
            ->assertDispatched('mostrar-error');

        $this->assertTrue($unicoAdministrador->fresh()->tieneRol('administrador'));
    }
}
