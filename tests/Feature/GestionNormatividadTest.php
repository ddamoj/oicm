<?php

namespace Tests\Feature;

use App\Livewire\Admin\Normatividad\FormularioNormatividad;
use App\Livewire\Admin\Normatividad\ListaNormatividad;
use App\Models\Normatividad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Alta, edición, validación y baja del marco normativo (Fase 7).
 */
class GestionNormatividadTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_de_contenido_puede_crear_un_ordenamiento_valido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioNormatividad::class)
            ->set('ambito', 'municipal')
            ->set('titulo', 'Bando de Policía y Gobierno')
            ->set('fechaPublicacion', '2025-01-01')
            ->set('fechaUltimaReforma', '2025-07-15')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('normatividad', [
            'titulo' => 'Bando de Policía y Gobierno',
            'ambito' => 'municipal',
            'vigente' => true,
        ]);
    }

    public function test_rechaza_una_reforma_anterior_a_la_publicacion(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioNormatividad::class)
            ->set('titulo', 'Ordenamiento con fechas inválidas')
            ->set('fechaPublicacion', '2025-06-01')
            ->set('fechaUltimaReforma', '2020-01-01')
            ->call('guardar')
            ->assertHasErrors(['fechaUltimaReforma']);

        $this->assertDatabaseMissing('normatividad', ['titulo' => 'Ordenamiento con fechas inválidas']);
    }

    public function test_rechaza_un_esquema_de_url_peligroso(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioNormatividad::class)
            ->set('titulo', 'Ordenamiento con enlace malicioso')
            ->set('documentoUrl', 'javascript:alert(1)')
            ->call('guardar')
            ->assertHasErrors(['documentoUrl']);

        $this->assertDatabaseMissing('normatividad', ['titulo' => 'Ordenamiento con enlace malicioso']);
    }

    public function test_puede_editar_un_ordenamiento_existente(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        // Fechas explícitas y ordenadas: NormatividadFactory las genera de forma
        // independiente y podrían no cumplir "reforma >= publicación" por azar.
        $normatividad = Normatividad::factory()->create([
            'titulo' => 'Título original',
            'fecha_publicacion' => '2020-01-01',
            'fecha_ultima_reforma' => '2021-01-01',
        ]);

        Livewire::actingAs($usuario)
            ->test(FormularioNormatividad::class)
            ->call('prepararEdicion', $normatividad->id)
            ->set('titulo', 'Título actualizado')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('normatividad', ['id' => $normatividad->id, 'titulo' => 'Título actualizado']);
    }

    public function test_alternar_vigente_retira_el_ordenamiento_de_la_consulta_publica_sin_eliminarlo(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $normatividad = Normatividad::factory()->create(['vigente' => true]);

        Livewire::actingAs($usuario)
            ->test(ListaNormatividad::class)
            ->call('alternarVigente', $normatividad->id);

        $this->assertDatabaseHas('normatividad', ['id' => $normatividad->id, 'vigente' => false]);
        $this->assertCount(0, Normatividad::query()->publicado()->get());
    }

    public function test_la_baja_elimina_el_ordenamiento_de_la_consulta_publica(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $normatividad = Normatividad::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ListaNormatividad::class)
            ->call('eliminar', $normatividad->id);

        $this->assertSoftDeleted('normatividad', ['id' => $normatividad->id]);
    }

    public function test_usuario_sin_rol_de_contenido_no_puede_gestionar_normatividad(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ListaNormatividad::class)
            ->assertForbidden();
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_normatividad(): void
    {
        $this->get(route('admin.normatividad'))->assertRedirect(route('login'));
    }
}
