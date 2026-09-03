<?php

namespace Tests\Feature;

use App\Models\CategoriaDocumento;
use App\Models\CategoriaEnlace;
use App\Models\Documento;
use App\Models\Enlace;
use App\Models\Noticia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Página de inicio (Fase 8): accesos rápidos dinámicos, últimas noticias y
 * documentos recientes.
 */
class PaginaInicioTest extends TestCase
{
    use RefreshDatabase;

    public function test_muestra_los_enlaces_marcados_como_acceso_rapido(): void
    {
        $categoria = CategoriaEnlace::factory()->create();
        Enlace::factory()->create([
            'categoria_enlace_id' => $categoria->id,
            'nombre' => 'Declaración patrimonial',
            'activo' => true,
            'destacado_inicio' => true,
        ]);
        Enlace::factory()->create([
            'categoria_enlace_id' => $categoria->id,
            'nombre' => 'Enlace no destacado',
            'activo' => true,
            'destacado_inicio' => false,
        ]);

        $this->get(route('inicio'))
            ->assertOk()
            ->assertSee('Declaración patrimonial')
            ->assertDontSee('Enlace no destacado');
    }

    public function test_muestra_el_respaldo_estatico_cuando_no_hay_accesos_rapidos_marcados(): void
    {
        $this->get(route('inicio'))
            ->assertOk()
            ->assertSee('Documentos institucionales');
    }

    public function test_no_muestra_un_acceso_rapido_inactivo(): void
    {
        $categoria = CategoriaEnlace::factory()->create();
        Enlace::factory()->create([
            'categoria_enlace_id' => $categoria->id,
            'nombre' => 'Acceso inactivo',
            'activo' => false,
            'destacado_inicio' => true,
        ]);

        $this->get(route('inicio'))->assertDontSee('Acceso inactivo');
    }

    public function test_muestra_documentos_recientes_publicados(): void
    {
        $categoria = CategoriaDocumento::factory()->create();
        Documento::factory()->create([
            'categoria_documento_id' => $categoria->id,
            'nombre' => 'Manual de organización 2026',
            'publicado' => true,
        ]);
        Documento::factory()->create([
            'categoria_documento_id' => $categoria->id,
            'nombre' => 'Borrador interno',
            'publicado' => false,
        ]);

        $this->get(route('inicio'))
            ->assertSee('Manual de organización 2026')
            ->assertDontSee('Borrador interno');
    }

    public function test_muestra_las_ultimas_noticias_publicadas(): void
    {
        Noticia::factory()->create(['titulo' => 'Noticia de portada']);

        $this->get(route('inicio'))->assertSee('Noticia de portada');
    }
}
