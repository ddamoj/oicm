<?php

namespace Tests\Feature;

use App\Livewire\Admin\Enlaces\FormularioEnlace;
use App\Livewire\Admin\Enlaces\ListaEnlaces;
use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Alta, edición, validación de URL y baja de enlaces de interés (RF-ENL-001).
 */
class GestionEnlacesTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_de_contenido_puede_crear_un_enlace_valido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $categoria = CategoriaEnlace::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioEnlace::class)
            ->set('nombre', 'Declaración patrimonial')
            ->set('categoriaEnlaceId', (string) $categoria->id)
            ->set('url', 'https://declaraciones.municipiodeoaxaca.gob.mx/public/')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('enlaces', [
            'nombre' => 'Declaración patrimonial',
            'categoria_enlace_id' => $categoria->id,
            'url' => 'https://declaraciones.municipiodeoaxaca.gob.mx/public/',
            'activo' => true,
        ]);
    }

    public function test_rechaza_una_url_con_formato_invalido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $categoria = CategoriaEnlace::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioEnlace::class)
            ->set('nombre', 'Enlace roto')
            ->set('categoriaEnlaceId', (string) $categoria->id)
            ->set('url', 'no-es-una-url')
            ->call('guardar')
            ->assertHasErrors(['url']);

        $this->assertDatabaseMissing('enlaces', ['nombre' => 'Enlace roto']);
    }

    public function test_rechaza_un_esquema_de_url_peligroso(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $categoria = CategoriaEnlace::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioEnlace::class)
            ->set('nombre', 'Enlace malicioso')
            ->set('categoriaEnlaceId', (string) $categoria->id)
            ->set('url', 'javascript:alert(1)')
            ->call('guardar')
            ->assertHasErrors(['url']);

        $this->assertDatabaseMissing('enlaces', ['nombre' => 'Enlace malicioso']);
    }

    public function test_nombre_y_categoria_son_obligatorios(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioEnlace::class)
            ->set('nombre', '')
            ->set('categoriaEnlaceId', '')
            ->set('url', 'https://example.com')
            ->call('guardar')
            ->assertHasErrors(['nombre' => 'required', 'categoriaEnlaceId' => 'required']);
    }

    public function test_puede_editar_un_enlace_existente(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $categoria = CategoriaEnlace::factory()->create();
        $enlace = Enlace::factory()->create(['categoria_enlace_id' => $categoria->id, 'nombre' => 'Nombre original']);

        Livewire::actingAs($usuario)
            ->test(FormularioEnlace::class)
            ->call('prepararEdicion', $enlace->id)
            ->set('nombre', 'Nombre actualizado')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('enlaces', ['id' => $enlace->id, 'nombre' => 'Nombre actualizado']);
    }

    public function test_la_baja_retira_el_enlace_de_la_consulta_publica_de_inmediato(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $enlace = Enlace::factory()->create(['activo' => true]);

        Livewire::actingAs($usuario)
            ->test(ListaEnlaces::class)
            ->call('eliminar', $enlace->id);

        $this->assertSoftDeleted('enlaces', ['id' => $enlace->id]);
        $this->assertCount(0, Enlace::query()->publicado()->get());
    }

    public function test_alternar_activo_retira_el_enlace_de_la_vista_publica_sin_eliminarlo(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $enlace = Enlace::factory()->create(['activo' => true]);

        Livewire::actingAs($usuario)
            ->test(ListaEnlaces::class)
            ->call('alternarActivo', $enlace->id);

        $this->assertDatabaseHas('enlaces', ['id' => $enlace->id, 'activo' => false]);
        $this->assertCount(0, Enlace::query()->publicado()->get());
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_enlaces(): void
    {
        $this->get(route('admin.enlaces'))->assertRedirect(route('login'));
    }

    public function test_usuario_sin_rol_de_contenido_no_puede_gestionar_enlaces(): void
    {
        $usuario = User::factory()->create(); // sin rol asignado

        Livewire::actingAs($usuario)
            ->test(ListaEnlaces::class)
            ->assertForbidden();
    }
}
