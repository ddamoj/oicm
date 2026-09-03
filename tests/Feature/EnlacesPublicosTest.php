<?php

namespace Tests\Feature;

use App\Livewire\Publico\ListaEnlacesPublica;
use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Directorio público de enlaces de interés, agrupado por categoría (RF-ENL-002).
 */
class EnlacesPublicosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_directorio_agrupa_los_enlaces_por_categoria(): void
    {
        $categoriaUno = CategoriaEnlace::factory()->create(['nombre' => 'Transparencia', 'orden' => 1]);
        $categoriaDos = CategoriaEnlace::factory()->create(['nombre' => 'Normativa', 'orden' => 2]);

        Enlace::factory()->create(['categoria_enlace_id' => $categoriaUno->id, 'nombre' => 'Enlace A', 'activo' => true]);
        Enlace::factory()->create(['categoria_enlace_id' => $categoriaDos->id, 'nombre' => 'Enlace B', 'activo' => true]);

        Livewire::test(ListaEnlacesPublica::class)
            ->assertSee('Transparencia')
            ->assertSee('Normativa')
            ->assertSee('Enlace A')
            ->assertSee('Enlace B');
    }

    public function test_los_enlaces_inactivos_no_aparecen_en_el_directorio_publico(): void
    {
        $categoria = CategoriaEnlace::factory()->create();
        Enlace::factory()->create(['categoria_enlace_id' => $categoria->id, 'nombre' => 'Enlace oculto', 'activo' => false]);

        Livewire::test(ListaEnlacesPublica::class)
            ->assertDontSee('Enlace oculto');
    }

    public function test_los_enlaces_eliminados_no_aparecen_en_el_directorio_publico(): void
    {
        $categoria = CategoriaEnlace::factory()->create();
        $enlace = Enlace::factory()->create(['categoria_enlace_id' => $categoria->id, 'nombre' => 'Enlace eliminado']);
        $enlace->delete();

        Livewire::test(ListaEnlacesPublica::class)
            ->assertDontSee('Enlace eliminado');
    }

    public function test_el_enlace_abre_en_pestana_nueva_con_indicador_de_enlace_externo(): void
    {
        $categoria = CategoriaEnlace::factory()->create();
        Enlace::factory()->create([
            'categoria_enlace_id' => $categoria->id,
            'nombre' => 'Portal externo',
            'url' => 'https://ejemplo.gob.mx/portal',
            'activo' => true,
        ]);

        $componente = Livewire::test(ListaEnlacesPublica::class);

        $componente->assertSeeHtml('target="_blank"')
            ->assertSeeHtml('rel="noopener noreferrer"')
            ->assertSee('se abre en una pestaña nueva', false);
    }

    public function test_el_buscador_filtra_por_nombre(): void
    {
        $categoria = CategoriaEnlace::factory()->create();
        Enlace::factory()->create(['categoria_enlace_id' => $categoria->id, 'nombre' => 'Declaración patrimonial', 'activo' => true]);
        Enlace::factory()->create(['categoria_enlace_id' => $categoria->id, 'nombre' => 'Padrón de contratistas', 'activo' => true]);

        Livewire::test(ListaEnlacesPublica::class)
            ->set('busqueda', 'Declaración')
            ->assertSee('Declaración patrimonial')
            ->assertDontSee('Padrón de contratistas');
    }
}
