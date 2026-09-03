<?php

namespace Tests\Feature;

use App\Livewire\Publico\ListaEstradosPublica;
use App\Models\Estrado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Listado público numerado y consultable de los estrados digitales de la
 * DRACS (Fase 7), y su descarga anónima.
 */
class EstradosPublicosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_listado_publico_muestra_solo_estrados_activos(): void
    {
        Estrado::factory()->create(['numero' => 1, 'asunto' => 'Notificación visible', 'activo' => true]);
        Estrado::factory()->create(['numero' => 2, 'asunto' => 'Notificación retirada', 'activo' => false]);

        Livewire::test(ListaEstradosPublica::class)
            ->assertSee('Notificación visible')
            ->assertDontSee('Notificación retirada');
    }

    public function test_el_buscador_filtra_por_expediente(): void
    {
        Estrado::factory()->create(['numero' => 1, 'expediente' => 'OICM/DRACS/001/2026', 'asunto' => 'Asunto A', 'activo' => true]);
        Estrado::factory()->create(['numero' => 2, 'expediente' => 'OICM/DRACS/002/2026', 'asunto' => 'Asunto B', 'activo' => true]);

        Livewire::test(ListaEstradosPublica::class)
            ->set('busqueda', '001/2026')
            ->assertSee('Asunto A')
            ->assertDontSee('Asunto B');
    }

    public function test_la_descarga_publica_de_un_estrado_activo_funciona_sin_autenticacion(): void
    {
        Storage::fake('documentos');
        Storage::disk('documentos')->put('estrados/2026/prueba.pdf', 'contenido de prueba');

        $estrado = Estrado::factory()->create([
            'archivo_ruta' => 'estrados/2026/prueba.pdf',
            'archivo_nombre' => 'prueba.pdf',
            'activo' => true,
        ]);

        $respuesta = $this->get(route('estrados.descargar', $estrado));

        $respuesta->assertOk();
    }

    public function test_no_se_puede_descargar_un_estrado_retirado(): void
    {
        Storage::fake('documentos');
        Storage::disk('documentos')->put('estrados/2026/prueba.pdf', 'contenido de prueba');

        $estrado = Estrado::factory()->create([
            'archivo_ruta' => 'estrados/2026/prueba.pdf',
            'archivo_nombre' => 'prueba.pdf',
            'activo' => false,
        ]);

        $this->get(route('estrados.descargar', $estrado))->assertNotFound();
    }
}
