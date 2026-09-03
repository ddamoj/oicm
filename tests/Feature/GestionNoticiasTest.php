<?php

namespace Tests\Feature;

use App\Livewire\Admin\Noticias\FormularioNoticia;
use App\Livewire\Admin\Noticias\ListaNoticias;
use App\Models\Noticia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Alta, edición y saneado de noticias, avisos y comunicados (RF-NOT-001/002).
 */
class GestionNoticiasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_administrador_de_contenido_puede_crear_una_noticia(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioNoticia::class)
            ->set('titulo', 'El OICM presenta su informe anual')
            ->set('contenido', '<p>Contenido de la noticia.</p>')
            ->set('estatus', 'borrador')
            ->call('guardar')
            ->assertHasNoErrors();

        $noticia = Noticia::query()->where('titulo', 'El OICM presenta su informe anual')->firstOrFail();

        $this->assertSame('el-oicm-presenta-su-informe-anual', $noticia->slug);
        $this->assertSame($usuario->id, $noticia->autor_id);
        $this->assertSame('borrador', $noticia->estatus);
    }

    public function test_el_html_malicioso_del_editor_se_guarda_saneado(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioNoticia::class)
            ->set('titulo', 'Aviso con contenido malicioso')
            ->set('contenido', '<p>Texto seguro</p><script>alert(1)</script><img src=x onerror=alert(1)>')
            ->set('estatus', 'borrador')
            ->call('guardar')
            ->assertHasNoErrors();

        $noticia = Noticia::query()->where('titulo', 'Aviso con contenido malicioso')->firstOrFail();

        $this->assertStringNotContainsString('<script', $noticia->contenido);
        $this->assertStringNotContainsString('onerror', $noticia->contenido);
        $this->assertStringContainsString('Texto seguro', $noticia->contenido);
    }

    public function test_al_subir_imagen_el_texto_alternativo_es_obligatorio(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioNoticia::class)
            ->set('titulo', 'Noticia con imagen sin alt')
            ->set('contenido', '<p>Contenido.</p>')
            ->set('estatus', 'borrador')
            ->set('imagen', UploadedFile::fake()->image('portada.jpg', 800, 600))
            ->call('guardar')
            ->assertHasErrors(['imagenAlt']);

        $this->assertDatabaseMissing('noticias', ['titulo' => 'Noticia con imagen sin alt']);
    }

    public function test_crea_una_noticia_con_imagen_y_genera_miniatura(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioNoticia::class)
            ->set('titulo', 'Noticia con imagen de portada')
            ->set('contenido', '<p>Contenido.</p>')
            ->set('estatus', 'borrador')
            ->set('imagen', UploadedFile::fake()->image('portada.jpg', 800, 600))
            ->set('imagenAlt', 'Fotografía del recinto del OICM')
            ->call('guardar')
            ->assertHasNoErrors();

        $noticia = Noticia::query()->where('titulo', 'Noticia con imagen de portada')->firstOrFail();

        $this->assertNotNull($noticia->imagen_portada);
        $this->assertNotNull($noticia->imagen_miniatura);
        Storage::disk('public')->assertExists($noticia->imagen_portada);
        Storage::disk('public')->assertExists($noticia->imagen_miniatura);
    }

    public function test_rechaza_un_archivo_que_no_es_imagen(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioNoticia::class)
            ->set('titulo', 'Noticia con archivo inválido')
            ->set('contenido', '<p>Contenido.</p>')
            ->set('estatus', 'borrador')
            ->set('imagen', UploadedFile::fake()->create('virus.exe', 100, 'application/x-msdownload'))
            ->call('guardar')
            ->assertHasErrors(['imagen']);

        $this->assertDatabaseMissing('noticias', ['titulo' => 'Noticia con archivo inválido']);
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_noticias(): void
    {
        $this->get(route('admin.noticias'))->assertRedirect(route('login'));
    }

    public function test_usuario_sin_rol_de_contenido_no_puede_gestionar_noticias(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ListaNoticias::class)
            ->assertForbidden();
    }
}
