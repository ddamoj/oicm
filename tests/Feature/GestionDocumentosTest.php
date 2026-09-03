<?php

namespace Tests\Feature;

use App\Livewire\Admin\Documentos\FormularioDocumento;
use App\Livewire\Admin\Documentos\ListaDocumentos;
use App\Models\CategoriaDocumento;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Carga, validación, versionado y administración de documentos (RF-CAR-001/002/003).
 */
class GestionDocumentosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Aísla las pruebas del disco real: nada se escribe fuera del sandbox de tests.
        Storage::fake('documentos');
    }

    public function test_administrador_de_contenido_puede_cargar_un_documento_valido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $categoria = CategoriaDocumento::factory()->create(['clave' => 'formatos']);

        Livewire::actingAs($usuario)
            ->test(FormularioDocumento::class)
            ->set('nombre', 'Formato de solicitud de auditoría')
            ->set('descripcion', 'Formato oficial para iniciar una auditoría interna.')
            ->set('categoriaDocumentoId', (string) $categoria->id)
            ->set('archivo', UploadedFile::fake()->create('formato.pdf', 500, 'application/pdf'))
            ->call('guardar')
            ->assertHasNoErrors();

        $documento = Documento::query()->where('nombre', 'Formato de solicitud de auditoría')->firstOrFail();

        $this->assertSame($categoria->id, $documento->categoria_documento_id);
        $this->assertSame($usuario->id, $documento->subido_por);
        Storage::disk('documentos')->assertExists($documento->ruta_archivo);
    }

    public function test_rechaza_extension_no_permitida_sin_almacenar_nada(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $categoria = CategoriaDocumento::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioDocumento::class)
            ->set('nombre', 'Archivo malicioso')
            ->set('categoriaDocumentoId', (string) $categoria->id)
            ->set('archivo', UploadedFile::fake()->create('virus.exe', 100, 'application/x-msdownload'))
            ->call('guardar')
            ->assertHasErrors(['archivo']);

        $this->assertDatabaseMissing('documentos', ['nombre' => 'Archivo malicioso']);
        Storage::disk('documentos')->assertDirectoryEmpty($categoria->clave);
    }

    public function test_rechaza_archivo_que_supera_el_tamano_maximo(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $categoria = CategoriaDocumento::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioDocumento::class)
            ->set('nombre', 'Base de datos enorme')
            ->set('categoriaDocumentoId', (string) $categoria->id)
            // 25 MB = 25600 KB: un archivo de 25601 KB debe rechazarse.
            ->set('archivo', UploadedFile::fake()->create('grande.csv', 25601, 'text/csv'))
            ->call('guardar')
            ->assertHasErrors(['archivo']);

        $this->assertDatabaseMissing('documentos', ['nombre' => 'Base de datos enorme']);
    }

    public function test_reemplazar_el_archivo_conserva_la_version_anterior(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $categoria = CategoriaDocumento::factory()->create();

        $documento = Documento::factory()->create([
            'categoria_documento_id' => $categoria->id,
            'ruta_archivo' => $categoria->clave.'/original.pdf',
            'nombre_original' => 'original.pdf',
        ]);
        Storage::disk('documentos')->put($documento->ruta_archivo, 'contenido original');

        Livewire::actingAs($usuario)
            ->test(FormularioDocumento::class)
            ->call('prepararReemplazo', $documento->id)
            ->set('archivo', UploadedFile::fake()->create('actualizado.pdf', 200, 'application/pdf'))
            ->call('guardar')
            ->assertHasNoErrors();

        $documento->refresh();

        $this->assertDatabaseHas('documento_versiones', [
            'documento_id' => $documento->id,
            'numero_version' => 1,
            'ruta_archivo' => $categoria->clave.'/original.pdf',
        ]);
        Storage::disk('documentos')->assertExists($categoria->clave.'/original.pdf');
        $this->assertNotSame($categoria->clave.'/original.pdf', $documento->ruta_archivo);
        Storage::disk('documentos')->assertExists($documento->ruta_archivo);
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_documentos(): void
    {
        $this->get(route('admin.documentos'))->assertRedirect(route('login'));
    }

    public function test_usuario_sin_rol_de_contenido_no_puede_gestionar_documentos(): void
    {
        $usuario = User::factory()->create(); // sin rol asignado

        Livewire::actingAs($usuario)
            ->test(ListaDocumentos::class)
            ->assertForbidden();
    }
}
