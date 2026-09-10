<?php

namespace Tests\Feature;

use App\Livewire\Admin\Estadisticas\TableroVisitas;
use App\Models\User;
use App\Models\VisitaPagina;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Módulo de visitas: registro automático de las páginas públicas y tablero
 * de estadísticas del panel. La medición es propia y anónima — no hay
 * cookies ni servicios de terceros.
 */
class EstadisticasVisitasTest extends TestCase
{
    use RefreshDatabase;

    /** Agente de un navegador real: sin esto la visita se descarta por robot. */
    private const AGENTE_NAVEGADOR = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';

    private function visitar(string $ruta, string $agente = self::AGENTE_NAVEGADOR): TestResponse
    {
        return $this->withHeaders(['User-Agent' => $agente])->get($ruta);
    }

    // ── Registro de visitas ──────────────────────────────────

    public function test_registra_la_visita_a_una_pagina_publica(): void
    {
        $this->visitar('/noticias')->assertOk();

        $this->assertDatabaseHas('visitas_pagina', ['ruta' => '/noticias']);
    }

    public function test_registra_la_visita_a_la_portada(): void
    {
        $this->visitar('/')->assertOk();

        $this->assertDatabaseHas('visitas_pagina', ['ruta' => '/']);
    }

    public function test_no_registra_a_los_rastreadores_automaticos(): void
    {
        $this->visitar('/', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
        $this->visitar('/', 'curl/8.4.0');
        $this->visitar('/', '');

        $this->assertDatabaseCount('visitas_pagina', 0);
    }

    /**
     * El panel es trabajo interno, no consulta ciudadana: no debe inflar las
     * estadísticas del micrositio público.
     */
    public function test_no_registra_las_paginas_del_panel_administrativo(): void
    {
        $usuario = User::factory()->administrador()->create();

        $this->actingAs($usuario)
            ->withHeaders(['User-Agent' => self::AGENTE_NAVEGADOR])
            ->get(route('admin.panel'))
            ->assertOk();

        $this->assertDatabaseCount('visitas_pagina', 0);
    }

    public function test_no_registra_las_paginas_de_autenticacion(): void
    {
        $this->visitar('/login')->assertOk();

        $this->assertDatabaseCount('visitas_pagina', 0);
    }

    public function test_no_registra_una_pagina_inexistente(): void
    {
        $this->visitar('/ruta-que-no-existe')->assertNotFound();

        $this->assertDatabaseCount('visitas_pagina', 0);
    }

    public function test_detecta_el_tipo_de_dispositivo(): void
    {
        $this->visitar('/', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15');
        $this->visitar('/noticias', 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15');
        $this->visitar('/documentos');

        $this->assertDatabaseHas('visitas_pagina', ['ruta' => '/', 'dispositivo' => 'movil']);
        $this->assertDatabaseHas('visitas_pagina', ['ruta' => '/noticias', 'dispositivo' => 'tableta']);
        $this->assertDatabaseHas('visitas_pagina', ['ruta' => '/documentos', 'dispositivo' => 'escritorio']);
    }

    /**
     * La dirección IP nunca se guarda en claro. Se usa HMAC con `APP_KEY`
     * como llave: un SHA-256 sin sal sería reversible por fuerza bruta sobre
     * las 2^32 direcciones IPv4, así que no anonimizaría de verdad.
     */
    public function test_la_ip_se_guarda_como_huella_irreversible_y_con_llave(): void
    {
        $this->visitar('/');

        $visita = VisitaPagina::query()->firstOrFail();

        $this->assertSame(64, strlen($visita->ip_huella));
        $this->assertStringNotContainsString('127.0.0.1', $visita->ip_huella);
        $this->assertNotSame(hash('sha256', '127.0.0.1'), $visita->ip_huella);
        $this->assertSame(VisitaPagina::huellaDe('127.0.0.1'), $visita->ip_huella);
    }

    // ── Scopes del modelo ────────────────────────────────────

    public function test_los_scopes_acotan_por_periodo(): void
    {
        VisitaPagina::factory()->create();
        VisitaPagina::factory()->haceDias(3)->create();
        VisitaPagina::factory()->haceDias(45)->create();

        $this->assertSame(1, VisitaPagina::query()->hoy()->count());
        $this->assertSame(2, VisitaPagina::query()->ultimosDias(7)->count());
        $this->assertSame(3, VisitaPagina::query()->count());
    }

    public function test_el_scope_por_ruta_filtra_una_sola_pagina(): void
    {
        VisitaPagina::factory()->create(['ruta' => '/documentos']);
        VisitaPagina::factory()->count(2)->create(['ruta' => '/noticias']);

        $this->assertSame(2, VisitaPagina::query()->porRuta('/noticias')->count());
    }

    // ── Tablero de estadísticas ──────────────────────────────

    public function test_el_tablero_calcula_los_indicadores(): void
    {
        $usuario = User::factory()->administrador()->create();

        VisitaPagina::factory()->count(3)->create(['ip_huella' => 'huella-repetida']);
        VisitaPagina::factory()->create(['ip_huella' => 'otra-huella']);

        $indicadores = Livewire::actingAs($usuario)->test(TableroVisitas::class)->instance()->indicadores();

        $this->assertSame(4, $indicadores['hoy']);
        $this->assertSame(2, $indicadores['unicosHoy']);
        $this->assertSame(4, $indicadores['total']);
    }

    /**
     * La serie diaria debe rellenar con cero los días sin visitas: si no, la
     * gráfica comprime el eje y oculta las caídas reales.
     */
    public function test_la_serie_diaria_rellena_los_dias_sin_visitas(): void
    {
        $usuario = User::factory()->administrador()->create();
        VisitaPagina::factory()->create();

        $serie = Livewire::actingAs($usuario)
            ->test(TableroVisitas::class)
            ->set('periodo', '7')
            ->instance()
            ->porDia();

        $this->assertCount(7, $serie);
        $this->assertSame(1, $serie[6]['total']);          // hoy
        $this->assertSame(0, $serie[0]['total']);          // hace seis días
    }

    public function test_el_tablero_ordena_las_paginas_mas_visitadas(): void
    {
        $usuario = User::factory()->administrador()->create();

        VisitaPagina::factory()->count(5)->create(['ruta' => '/noticias']);
        VisitaPagina::factory()->count(2)->create(['ruta' => '/documentos']);

        $top = Livewire::actingAs($usuario)->test(TableroVisitas::class)->instance()->topPaginas();

        $this->assertSame('/noticias', $top[0]['ruta']);
        $this->assertSame(5, $top[0]['total']);
        $this->assertSame('/documentos', $top[1]['ruta']);
    }

    public function test_el_reparto_por_dispositivo_suma_cien_por_ciento(): void
    {
        $usuario = User::factory()->administrador()->create();

        VisitaPagina::factory()->count(3)->create(['dispositivo' => 'escritorio']);
        VisitaPagina::factory()->create(['dispositivo' => 'movil']);

        $reparto = collect(Livewire::actingAs($usuario)->test(TableroVisitas::class)->instance()->porDispositivo())
            ->keyBy('tipo');

        $this->assertSame(75.0, $reparto['escritorio']['porcentaje']);
        $this->assertSame(25.0, $reparto['movil']['porcentaje']);
        $this->assertSame(0.0, $reparto['tableta']['porcentaje']);
    }

    /**
     * La tendencia anual se agrupa con `DATE_FORMAT` (MySQL): el módulo de
     * origen usaba `EXTRACT(... FROM ...)`, sintaxis de PostgreSQL.
     */
    public function test_la_tendencia_mensual_cubre_doce_meses(): void
    {
        $usuario = User::factory()->administrador()->create();
        VisitaPagina::factory()->count(2)->create();

        $serie = Livewire::actingAs($usuario)->test(TableroVisitas::class)->instance()->porMes();

        $this->assertCount(12, $serie);
        $this->assertSame(2, $serie[11]['total']); // mes en curso
    }

    /** Un periodo manipulado en la URL no debe llegar a la consulta. */
    public function test_un_periodo_invalido_cae_al_valor_por_defecto(): void
    {
        $usuario = User::factory()->administrador()->create();
        VisitaPagina::factory()->create();

        $serie = Livewire::actingAs($usuario)
            ->test(TableroVisitas::class)
            ->set('periodo', '999 OR 1=1')
            ->instance()
            ->porDia();

        $this->assertCount(30, $serie);
    }

    // ── Autorización ─────────────────────────────────────────

    public function test_visitante_anonimo_no_accede_al_tablero(): void
    {
        $this->get(route('admin.estadisticas'))->assertRedirect(route('login'));
    }

    public function test_ambos_roles_de_gestion_pueden_consultar_el_tablero(): void
    {
        foreach ([User::factory()->administrador()->create(), User::factory()->administradorContenido()->create()] as $usuario) {
            Livewire::actingAs($usuario)
                ->test(TableroVisitas::class)
                ->assertOk()
                ->assertSee('Visitas hoy');
        }
    }

    public function test_usuario_sin_rol_no_accede_al_tablero(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(TableroVisitas::class)
            ->assertForbidden();
    }
}
