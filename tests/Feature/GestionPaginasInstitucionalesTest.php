<?php

namespace Tests\Feature;

use App\Livewire\Admin\Paginas\FormularioPaginaInstitucional;
use App\Livewire\Admin\Paginas\HistorialPagina;
use App\Livewire\Admin\Paginas\ListaPaginasInstitucionales;
use App\Models\PaginaInstitucional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Editor de contenido institucional con bloques versionados (Fase 7).
 */
class GestionPaginasInstitucionalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_de_contenido_puede_crear_una_pagina(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioPaginaInstitucional::class)
            ->set('titulo', 'Historia del OICM')
            ->set('contenido', '<p>Texto institucional.</p>')
            ->set('estatus', 'publicada')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('paginas_institucionales', [
            'titulo' => 'Historia del OICM',
            'estatus' => 'publicada',
        ]);
    }

    public function test_editar_una_pagina_archiva_el_contenido_anterior_como_version(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $pagina = PaginaInstitucional::factory()->create(['titulo' => 'Original', 'contenido' => '<p>Contenido original</p>']);

        Livewire::actingAs($usuario)
            ->test(FormularioPaginaInstitucional::class)
            ->call('prepararEdicion', $pagina->id)
            ->set('titulo', 'Actualizado')
            ->set('contenido', '<p>Contenido nuevo</p>')
            ->call('guardar')
            ->assertHasNoErrors();

        $pagina->refresh();

        $this->assertSame('Actualizado', $pagina->titulo);
        $this->assertDatabaseHas('pagina_institucional_versiones', [
            'pagina_institucional_id' => $pagina->id,
            'titulo' => 'Original',
            'numero_version' => 1,
        ]);
    }

    public function test_preparar_edicion_envia_el_contenido_al_editor_wire_ignore(): void
    {
        // El contenedor de Quill usa wire:ignore, así que Livewire nunca lo
        // vuelve a renderizar: el HTML real solo puede llegarle a través de
        // este evento de navegador, no del valor inicial de Alpine.
        $usuario = User::factory()->administradorContenido()->create();
        $pagina = PaginaInstitucional::factory()->create(['contenido' => '<p>Contenido real</p>']);

        Livewire::actingAs($usuario)
            ->test(FormularioPaginaInstitucional::class)
            ->call('prepararEdicion', $pagina->id)
            ->assertDispatched('editor:contenido:cargar', contenido: '<p>Contenido real</p>');
    }

    public function test_restaurar_una_version_desde_el_historial(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $pagina = PaginaInstitucional::factory()->create(['titulo' => 'Versión 1', 'contenido' => '<p>Uno</p>']);

        Livewire::actingAs($usuario)
            ->test(FormularioPaginaInstitucional::class)
            ->call('prepararEdicion', $pagina->id)
            ->set('titulo', 'Versión 2')
            ->set('contenido', '<p>Dos</p>')
            ->call('guardar');

        $pagina->refresh();
        $primeraVersion = $pagina->versiones()->where('numero_version', 1)->firstOrFail();

        Livewire::actingAs($usuario)
            ->test(HistorialPagina::class)
            ->call('abrir', $pagina->id)
            ->call('restaurar', $primeraVersion->id);

        $pagina->refresh();
        $this->assertSame('Versión 1', $pagina->titulo);
    }

    public function test_el_contenido_se_sanea_al_guardar(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioPaginaInstitucional::class)
            ->set('titulo', 'Página con script')
            ->set('contenido', '<p>Texto</p><script>alert(1)</script>')
            ->call('guardar')
            ->assertHasNoErrors();

        $pagina = PaginaInstitucional::query()->where('titulo', 'Página con script')->firstOrFail();

        $this->assertStringNotContainsString('<script>', $pagina->contenido);
    }

    public function test_despublicar_retira_la_pagina_de_la_consulta_publica_sin_eliminarla(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $pagina = PaginaInstitucional::factory()->create(['estatus' => 'publicada']);

        Livewire::actingAs($usuario)
            ->test(ListaPaginasInstitucionales::class)
            ->call('despublicar', $pagina->id);

        $this->assertDatabaseHas('paginas_institucionales', ['id' => $pagina->id, 'estatus' => 'borrador']);
        $this->assertCount(0, PaginaInstitucional::query()->publicado()->where('id', $pagina->id)->get());
    }

    public function test_usuario_sin_rol_de_contenido_no_puede_gestionar_paginas(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ListaPaginasInstitucionales::class)
            ->assertForbidden();
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_paginas(): void
    {
        $this->get(route('admin.paginas'))->assertRedirect(route('login'));
    }
}
