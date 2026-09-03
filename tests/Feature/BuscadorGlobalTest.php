<?php

namespace Tests\Feature;

use App\Livewire\Publico\BuscadorGlobal;
use App\Models\CategoriaDocumento;
use App\Models\Documento;
use App\Models\Noticia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Buscador global del micrositio: consulta varios modelos a la vez,
 * respetando siempre la visibilidad pública de cada uno (Fase 8).
 */
class BuscadorGlobalTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_ejecuta_busqueda_con_menos_de_tres_caracteres(): void
    {
        Noticia::factory()->create(['titulo' => 'Aviso oficial de auditoría']);

        Livewire::test(BuscadorGlobal::class)
            ->set('termino', 'au')
            ->assertSee('al menos 3 caracteres');
    }

    public function test_encuentra_una_noticia_publicada_por_titulo(): void
    {
        Noticia::factory()->create(['titulo' => 'Aviso oficial de auditoría interna']);

        Livewire::test(BuscadorGlobal::class)
            ->set('termino', 'auditoría')
            ->assertSee('Aviso oficial de auditoría interna');
    }

    public function test_no_muestra_una_noticia_en_borrador(): void
    {
        Noticia::factory()->create(['titulo' => 'Borrador de auditoría interna', 'estatus' => 'borrador', 'publicado_en' => null]);

        Livewire::test(BuscadorGlobal::class)
            ->set('termino', 'auditoría')
            ->assertDontSee('Borrador de auditoría interna');
    }

    public function test_no_muestra_un_documento_no_publicado(): void
    {
        $categoria = CategoriaDocumento::factory()->create();
        Documento::factory()->create([
            'categoria_documento_id' => $categoria->id,
            'nombre' => 'Formato de auditoría confidencial',
            'publicado' => false,
        ]);

        Livewire::test(BuscadorGlobal::class)
            ->set('termino', 'auditoría')
            ->assertDontSee('Formato de auditoría confidencial');
    }

    public function test_sin_resultados_muestra_el_estado_vacio(): void
    {
        Livewire::test(BuscadorGlobal::class)
            ->set('termino', 'palabraquenoexisteenningunlado')
            ->assertSee('Sin resultados');
    }
}
