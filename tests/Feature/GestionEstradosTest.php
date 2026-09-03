<?php

namespace Tests\Feature;

use App\Livewire\Admin\Estrados\FormularioEstrado;
use App\Livewire\Admin\Estrados\ListaEstrados;
use App\Models\Estrado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Publicación, consecutivo, constancia y administración de los estrados
 * digitales de la DRACS (Fase 7).
 */
class GestionEstradosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('documentos');
    }

    public function test_administrador_de_contenido_puede_publicar_un_estrado_valido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioEstrado::class)
            ->set('asunto', 'Notificación de acuerdo de radicación')
            ->set('expediente', 'OICM/DRACS/001/2026')
            ->set('archivo', UploadedFile::fake()->create('notificacion.pdf', 200, 'application/pdf'))
            ->set('datosTestados', true)
            ->call('guardar')
            ->assertHasNoErrors();

        $estrado = Estrado::query()->where('asunto', 'Notificación de acuerdo de radicación')->firstOrFail();

        $this->assertSame(1, $estrado->numero);
        $this->assertSame($usuario->id, $estrado->publicado_por);
        $this->assertNotEmpty($estrado->archivo_hash);
        Storage::disk('documentos')->assertExists($estrado->archivo_ruta);
    }

    public function test_el_numero_de_folio_es_consecutivo(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        Estrado::factory()->create(['numero' => 5]);

        Livewire::actingAs($usuario)
            ->test(FormularioEstrado::class)
            ->set('asunto', 'Segunda notificación')
            ->set('archivo', UploadedFile::fake()->create('notificacion.pdf', 200, 'application/pdf'))
            ->set('datosTestados', true)
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('estrados', ['asunto' => 'Segunda notificación', 'numero' => 6]);
    }

    public function test_rechaza_un_archivo_que_no_sea_pdf(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioEstrado::class)
            ->set('asunto', 'Archivo inválido')
            ->set('archivo', UploadedFile::fake()->create('notificacion.docx', 200, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
            ->set('datosTestados', true)
            ->call('guardar')
            ->assertHasErrors(['archivo']);

        $this->assertDatabaseMissing('estrados', ['asunto' => 'Archivo inválido']);
    }

    public function test_exige_confirmar_que_los_datos_personales_fueron_testados(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioEstrado::class)
            ->set('asunto', 'Sin confirmar testado')
            ->set('archivo', UploadedFile::fake()->create('notificacion.pdf', 200, 'application/pdf'))
            ->set('datosTestados', false)
            ->call('guardar')
            ->assertHasErrors(['datosTestados']);

        $this->assertDatabaseMissing('estrados', ['asunto' => 'Sin confirmar testado']);
    }

    public function test_alternar_activo_retira_el_estrado_de_la_consulta_publica_sin_eliminarlo(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $estrado = Estrado::factory()->create(['activo' => true]);

        Livewire::actingAs($usuario)
            ->test(ListaEstrados::class)
            ->call('alternarActivo', $estrado->id);

        $this->assertDatabaseHas('estrados', ['id' => $estrado->id, 'activo' => false]);
        $this->assertCount(0, Estrado::query()->publicado()->get());
    }

    public function test_usuario_sin_rol_de_contenido_no_puede_gestionar_estrados(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ListaEstrados::class)
            ->assertForbidden();
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_estrados(): void
    {
        $this->get(route('admin.estrados'))->assertRedirect(route('login'));
    }
}
