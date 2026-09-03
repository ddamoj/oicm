<?php

namespace Tests\Feature;

use App\Livewire\Admin\Galerias\FormularioGaleria;
use App\Livewire\Admin\Galerias\ListaGalerias;
use App\Livewire\Admin\Galerias\MediosGaleria;
use App\Models\Galeria;
use App\Models\GaleriaMedio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Alta, edición, baja y gestión de medios de galerías de fotos y videos (Fase 8).
 */
class GestionGaleriasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_administrador_de_contenido_puede_crear_una_galeria(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioGaleria::class)
            ->set('titulo', 'Sesión ordinaria del COCODI')
            ->set('descripcion', 'Seguimiento a los acuerdos del comité.')
            ->set('fechaEvento', '2026-03-05')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('galerias', [
            'titulo' => 'Sesión ordinaria del COCODI',
            'publicada' => true,
        ]);
    }

    public function test_titulo_es_obligatorio(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioGaleria::class)
            ->set('titulo', '')
            ->call('guardar')
            ->assertHasErrors(['titulo' => 'required']);
    }

    public function test_puede_subir_una_foto_valida_a_la_galeria(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $galeria = Galeria::factory()->create();

        Livewire::actingAs($usuario)
            ->test(MediosGaleria::class, ['galeria' => $galeria])
            ->set('tipo', 'foto')
            ->set('archivo', UploadedFile::fake()->image('evento.jpg', 800, 600))
            ->set('descripcionAlt', 'Personas asistentes a la capacitación')
            ->call('agregar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('galeria_medios', [
            'galeria_id' => $galeria->id,
            'tipo' => 'foto',
            'descripcion_alt' => 'Personas asistentes a la capacitación',
        ]);
    }

    public function test_rechaza_una_foto_con_extension_no_permitida(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $galeria = Galeria::factory()->create();

        Livewire::actingAs($usuario)
            ->test(MediosGaleria::class, ['galeria' => $galeria])
            ->set('tipo', 'foto')
            ->set('archivo', UploadedFile::fake()->create('archivo.exe', 100, 'application/x-msdownload'))
            ->set('descripcionAlt', 'Texto alternativo')
            ->call('agregar')
            ->assertHasErrors(['archivo']);

        $this->assertDatabaseCount('galeria_medios', 0);
    }

    public function test_texto_alternativo_es_obligatorio_para_fotos(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $galeria = Galeria::factory()->create();

        Livewire::actingAs($usuario)
            ->test(MediosGaleria::class, ['galeria' => $galeria])
            ->set('tipo', 'foto')
            ->set('archivo', UploadedFile::fake()->image('evento.jpg', 800, 600))
            ->set('descripcionAlt', '')
            ->call('agregar')
            ->assertHasErrors(['descripcionAlt' => 'required']);
    }

    public function test_puede_agregar_un_video_por_url_de_youtube(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $galeria = Galeria::factory()->create();

        Livewire::actingAs($usuario)
            ->test(MediosGaleria::class, ['galeria' => $galeria])
            ->set('tipo', 'video')
            ->set('urlExterna', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->call('agregar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('galeria_medios', [
            'galeria_id' => $galeria->id,
            'tipo' => 'video',
            'url_externa' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }

    public function test_rechaza_una_url_de_video_de_un_proveedor_no_permitido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $galeria = Galeria::factory()->create();

        Livewire::actingAs($usuario)
            ->test(MediosGaleria::class, ['galeria' => $galeria])
            ->set('tipo', 'video')
            ->set('urlExterna', 'https://malicioso.example.com/video')
            ->call('agregar')
            ->assertHasErrors(['urlExterna']);

        $this->assertDatabaseCount('galeria_medios', 0);
    }

    public function test_puede_subir_un_video_valido_a_la_galeria(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $galeria = Galeria::factory()->create();

        Livewire::actingAs($usuario)
            ->test(MediosGaleria::class, ['galeria' => $galeria])
            ->set('tipo', 'video')
            ->set('archivo', UploadedFile::fake()->create('evento.mp4', 500, 'video/mp4'))
            ->call('agregar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('galeria_medios', [
            'galeria_id' => $galeria->id,
            'tipo' => 'video',
        ]);
    }

    public function test_la_baja_elimina_la_galeria_y_sus_medios(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $galeria = Galeria::factory()->create();
        GaleriaMedio::factory()->create(['galeria_id' => $galeria->id]);

        Livewire::actingAs($usuario)
            ->test(ListaGalerias::class)
            ->call('eliminar', $galeria->id);

        $this->assertSoftDeleted('galerias', ['id' => $galeria->id]);
    }

    public function test_alternar_publicada_retira_la_galeria_de_la_vista_publica(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $galeria = Galeria::factory()->create(['publicada' => true]);

        Livewire::actingAs($usuario)
            ->test(ListaGalerias::class)
            ->call('alternarPublicada', $galeria->id);

        $this->assertDatabaseHas('galerias', ['id' => $galeria->id, 'publicada' => false]);
        $this->assertCount(0, Galeria::query()->publicado()->get());
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_galerias(): void
    {
        $this->get(route('admin.galerias'))->assertRedirect(route('login'));
    }

    public function test_usuario_sin_rol_de_contenido_no_puede_gestionar_galerias(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ListaGalerias::class)
            ->assertForbidden();
    }
}
