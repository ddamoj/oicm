<?php

namespace Tests\Unit;

use App\Models\PaginaInstitucional;
use App\Models\User;
use App\Services\VersionadorPaginas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Editor de contenido institucional con bloques versionados (Fase 7).
 */
class PaginaInstitucionalVersionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guardar_archiva_el_contenido_vigente_antes_de_sobrescribirlo(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $pagina = PaginaInstitucional::factory()->create(['titulo' => 'Original', 'contenido' => '<p>Contenido original</p>']);

        app(VersionadorPaginas::class)->guardar($pagina, ['titulo' => 'Actualizado', 'contenido' => '<p>Contenido nuevo</p>'], $usuario);

        $pagina->refresh();

        $this->assertSame('Actualizado', $pagina->titulo);
        $this->assertCount(1, $pagina->versiones);
        $this->assertSame('Original', $pagina->versiones->first()->titulo);
        $this->assertSame(1, $pagina->versiones->first()->numero_version);
        $this->assertTrue($pagina->versiones->first()->actualizadoPor->is($usuario));
    }

    public function test_restaurar_una_version_regresa_su_contenido_y_archiva_el_vigente(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $pagina = PaginaInstitucional::factory()->create(['titulo' => 'Versión 1', 'contenido' => '<p>Uno</p>']);
        $versionador = app(VersionadorPaginas::class);

        $versionador->guardar($pagina, ['titulo' => 'Versión 2', 'contenido' => '<p>Dos</p>'], $usuario);
        $pagina->refresh();
        $primeraVersion = $pagina->versiones()->where('numero_version', 1)->firstOrFail();

        $versionador->restaurar($pagina, $primeraVersion, $usuario);
        $pagina->refresh();

        $this->assertSame('Versión 1', $pagina->titulo);
        $this->assertSame('<p>Uno</p>', $pagina->contenido);
        $this->assertCount(2, $pagina->versiones);
    }
}
