<?php

namespace Tests\Feature;

use App\Livewire\Admin\Panel\ResumenPanel;
use App\Models\BitacoraAuditoria;
use App\Models\Documento;
use App\Models\Enlace;
use App\Models\Normatividad;
use App\Models\Noticia;
use App\Models\PaginaInstitucional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Panel principal de la administración: resumen de contenido publicado,
 * pendientes accionables y actividad reciente, con el alcance recortado por
 * rol.
 */
class PanelPrincipalTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_panel_cuenta_solo_el_contenido_visible_en_el_portal(): void
    {
        $usuario = User::factory()->administrador()->create();

        Documento::factory()->count(3)->create(['publicado' => true]);
        Documento::factory()->create(['publicado' => false]);
        Noticia::factory()->create(['estatus' => 'publicada', 'publicado_en' => now()->subDay()]);
        Noticia::factory()->create(['estatus' => 'borrador', 'publicado_en' => null]);
        Enlace::factory()->count(2)->create(['activo' => true]);
        Enlace::factory()->create(['activo' => false]);
        Normatividad::factory()->create(['vigente' => true]);

        $publicado = collect(Livewire::actingAs($usuario)->test(ResumenPanel::class)->instance()->publicado())
            ->keyBy('etiqueta');

        $this->assertSame(3, $publicado['Documentos']['total']);
        $this->assertSame('1 sin publicar', $publicado['Documentos']['detalle']);
        $this->assertSame(1, $publicado['Noticias']['total']);
        $this->assertSame(2, $publicado['Enlaces']['total']);
        $this->assertSame(1, $publicado['Normatividad']['total']);
    }

    /**
     * Una noticia "publicada" con fecha futura todavía no es visible: cuenta
     * como programada, no como publicada.
     */
    public function test_una_noticia_programada_a_futuro_no_cuenta_como_publicada(): void
    {
        $usuario = User::factory()->administrador()->create();
        Noticia::factory()->create(['estatus' => 'publicada', 'publicado_en' => now()->addWeek()]);

        $componente = Livewire::actingAs($usuario)->test(ResumenPanel::class)->instance();

        $publicado = collect($componente->publicado())->keyBy('etiqueta');
        $this->assertSame(0, $publicado['Noticias']['total']);

        $textos = collect($componente->pendientes())->pluck('texto')->all();
        $this->assertContains('1 noticia programada a futuro', $textos);
    }

    public function test_los_pendientes_listan_los_borradores_de_cada_modulo(): void
    {
        $usuario = User::factory()->administrador()->create();

        Noticia::factory()->count(2)->create(['estatus' => 'borrador', 'publicado_en' => null]);
        PaginaInstitucional::factory()->create(['estatus' => 'borrador']);
        Documento::factory()->create(['publicado' => false]);

        $textos = collect(Livewire::actingAs($usuario)->test(ResumenPanel::class)->instance()->pendientes())
            ->pluck('texto')
            ->all();

        $this->assertContains('2 noticias en borrador', $textos);
        $this->assertContains('1 página institucional en borrador', $textos);
        $this->assertContains('1 documento sin publicar', $textos);
    }

    public function test_sin_trabajo_a_medias_no_hay_pendientes(): void
    {
        $usuario = User::factory()->administrador()->create();
        Noticia::factory()->create(['estatus' => 'publicada', 'publicado_en' => now()->subDay()]);
        Documento::factory()->create(['publicado' => true]);

        Livewire::actingAs($usuario)
            ->test(ResumenPanel::class)
            ->assertSee('Todo al día');
    }

    public function test_el_administrador_ve_la_actividad_reciente_y_el_conteo_de_cuentas(): void
    {
        $usuario = User::factory()->administrador()->create();
        BitacoraAuditoria::create([
            'user_id' => $usuario->id,
            'accion' => 'noticia_creada',
            'modelo_afectado' => 'Noticia',
            'modelo_id' => 1,
            'detalle' => ['titulo' => 'Informe anual'],
        ]);

        $componente = Livewire::actingAs($usuario)->test(ResumenPanel::class);

        $this->assertCount(1, $componente->instance()->actividad());
        $this->assertSame(1, $componente->instance()->cuentas()['activas']);

        // La acción se humaniza en la vista: `noticia_creada` → "Noticia creada".
        $componente->assertSee('Noticia creada')->assertSee('Informe anual');
    }

    /**
     * Las acciones se guardan sin acentos por ser identificadores; al
     * mostrarlas deben leerse como español correcto.
     */
    public function test_las_acciones_se_muestran_con_su_acentuacion_correcta(): void
    {
        $usuario = User::factory()->administrador()->create();
        $panel = Livewire::actingAs($usuario)->test(ResumenPanel::class)->instance();

        $this->assertSame('Página institucional actualizada', $panel->etiquetaAccion('pagina_institucional_actualizada'));
        $this->assertSame('Dirección creada', $panel->etiquetaAccion('direccion_creada'));
        $this->assertSame('Noticia creada', $panel->etiquetaAccion('noticia_creada'));
    }

    /**
     * Un intento de acceso fallido no tiene sesión: no debe presentarse como
     * si perteneciera a una cuenta borrada.
     */
    public function test_distingue_una_accion_sin_sesion_de_una_cuenta_eliminada(): void
    {
        $usuario = User::factory()->administrador()->create();
        $eliminado = User::factory()->create();

        $sinSesion = BitacoraAuditoria::create([
            'user_id' => null,
            'accion' => 'login_fallido',
            'detalle' => null,
        ]);

        $deCuentaBorrada = BitacoraAuditoria::create([
            'user_id' => $eliminado->id,
            'accion' => 'noticia_creada',
            'detalle' => null,
        ]);

        // La baja de cuentas del sistema es lógica (`User` usa SoftDeletes):
        // la fila de bitácora conserva el `user_id`, pero la relación ya no
        // resuelve el nombre.
        $eliminado->delete();

        $panel = Livewire::actingAs($usuario)->test(ResumenPanel::class)->instance();

        $this->assertSame('Sin sesión iniciada', $panel->autorDe($sinSesion->fresh()));
        $this->assertSame('Cuenta eliminada', $panel->autorDe($deCuentaBorrada->fresh()));
    }

    /**
     * La bitácora y el padrón de cuentas son exclusivos del rol
     * "administrador": el panel no debe filtrarlos por la puerta de atrás.
     */
    public function test_el_administrador_de_contenido_no_ve_bitacora_ni_cuentas(): void
    {
        $administrador = User::factory()->administrador()->create();
        $contenido = User::factory()->administradorContenido()->create();

        BitacoraAuditoria::create([
            'user_id' => $administrador->id,
            'accion' => 'usuario_eliminado',
            'modelo_afectado' => 'User',
            'modelo_id' => $administrador->id,
            'detalle' => ['nombre' => 'Cuenta sensible'],
        ]);

        $componente = Livewire::actingAs($contenido)->test(ResumenPanel::class);

        $this->assertCount(0, $componente->instance()->actividad());
        $this->assertNull($componente->instance()->cuentas());
        $componente->assertDontSee('Cuenta sensible')
            ->assertDontSee('Actividad reciente')
            ->assertDontSee('Cuentas con acceso');
    }

    public function test_el_administrador_de_contenido_si_ve_el_resumen_de_contenido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        Documento::factory()->create(['publicado' => true]);

        Livewire::actingAs($usuario)
            ->test(ResumenPanel::class)
            ->assertOk()
            ->assertSee('Publicado en el portal');
    }

    public function test_visitante_anonimo_no_accede_al_panel(): void
    {
        $this->get(route('admin.panel'))->assertRedirect(route('login'));
    }

    public function test_usuario_sin_rol_no_accede_al_panel(): void
    {
        $usuario = User::factory()->create(); // sin rol asignado

        Livewire::actingAs($usuario)
            ->test(ResumenPanel::class)
            ->assertForbidden();
    }
}
