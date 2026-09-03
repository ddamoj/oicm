<?php

namespace Tests\Feature;

use App\Livewire\Publico\ListaDocumentosPublica;
use App\Models\CategoriaDocumento;
use App\Models\Documento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Vista y descarga pública del repositorio de documentos, sin autenticación
 * (RF-DES-001/002).
 */
class DescargaPublicaDocumentosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('documentos');
    }

    public function test_visitante_anonimo_puede_descargar_un_documento_publicado(): void
    {
        $documento = Documento::factory()->create([
            'publicado' => true,
            'ruta_archivo' => 'formatos/documento.pdf',
            'nombre_original' => 'Formato de solicitud.pdf',
            'contador_descargas' => 0,
        ]);
        Storage::disk('documentos')->put($documento->ruta_archivo, 'contenido de prueba');

        $respuesta = $this->get(route('documentos.descargar', $documento));

        $respuesta->assertOk();
        $respuesta->assertHeader('Content-Disposition');
        $this->assertStringContainsString('attachment', $respuesta->headers->get('Content-Disposition'));
        $this->assertSame(1, $documento->fresh()->contador_descargas);
    }

    public function test_no_permite_descargar_un_documento_no_publicado(): void
    {
        $documento = Documento::factory()->create([
            'publicado' => false,
            'ruta_archivo' => 'formatos/oculto.pdf',
        ]);
        Storage::disk('documentos')->put($documento->ruta_archivo, 'contenido');

        $this->get(route('documentos.descargar', $documento))->assertNotFound();
    }

    public function test_no_permite_descargar_un_documento_inexistente(): void
    {
        $this->get(route('documentos.descargar', 999999))->assertNotFound();
    }

    public function test_la_vista_publica_solo_muestra_documentos_publicados(): void
    {
        $publicado = Documento::factory()->create(['publicado' => true, 'nombre' => 'Formato visible']);
        Documento::factory()->create(['publicado' => false, 'nombre' => 'Formato oculto']);

        Livewire::test(ListaDocumentosPublica::class)
            ->assertSee('Formato visible')
            ->assertDontSee('Formato oculto');
    }

    public function test_filtra_por_categoria(): void
    {
        $formatos = CategoriaDocumento::factory()->create(['clave' => 'formatos', 'nombre' => 'Formatos']);
        $oficios = CategoriaDocumento::factory()->create(['clave' => 'oficios', 'nombre' => 'Oficios']);

        Documento::factory()->create(['categoria_documento_id' => $formatos->id, 'nombre' => 'Formato A', 'publicado' => true]);
        Documento::factory()->create(['categoria_documento_id' => $oficios->id, 'nombre' => 'Oficio B', 'publicado' => true]);

        Livewire::test(ListaDocumentosPublica::class)
            ->set('filtroCategoria', (string) $formatos->id)
            ->assertSee('Formato A')
            ->assertDontSee('Oficio B');
    }

    public function test_busqueda_sin_resultados_muestra_mensaje(): void
    {
        Documento::factory()->create(['nombre' => 'Padrón de contratistas', 'publicado' => true]);

        Livewire::test(ListaDocumentosPublica::class)
            ->set('busqueda', 'palabra que no existe en ningún documento')
            ->assertSee('Sin documentos que coincidan');
    }
}
