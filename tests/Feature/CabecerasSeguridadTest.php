<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cabeceras de seguridad HTTP (Fase 9): verifica que el middleware
 * CabecerasSeguridad se aplique tanto a rutas públicas como administrativas,
 * y que el CSP conserve el frame-src de Google Maps pendiente desde la
 * Fase 8 (plandetrabajo.md, cierre de la Fase 8).
 */
class CabecerasSeguridadTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_portada_publica_incluye_las_cabeceras_de_seguridad(): void
    {
        $respuesta = $this->get(route('inicio'));

        $respuesta->assertOk();
        $respuesta->assertHeader('X-Content-Type-Options', 'nosniff');
        $respuesta->assertHeader('X-Frame-Options', 'DENY');
        $respuesta->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $respuesta->assertHeader('Permissions-Policy');
        $respuesta->assertHeader('Content-Security-Policy');
    }

    public function test_el_panel_administrativo_tambien_incluye_las_cabeceras(): void
    {
        $usuario = User::factory()->administrador()->create();

        $respuesta = $this->actingAs($usuario)->get('/admin');

        $respuesta->assertOk();
        $respuesta->assertHeader('X-Frame-Options', 'DENY');
        $respuesta->assertHeader('Content-Security-Policy');
    }

    public function test_el_csp_permite_el_mapa_de_google_y_los_reproductores_de_video(): void
    {
        $csp = $this->get(route('inicio'))->headers->get('Content-Security-Policy');

        $this->assertStringContainsString('frame-src', $csp);
        $this->assertStringContainsString('https://www.google.com', $csp);
        $this->assertStringContainsString('https://www.youtube-nocookie.com', $csp);
        $this->assertStringContainsString('https://player.vimeo.com', $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
    }

    public function test_el_nonce_del_csp_coincide_con_el_de_las_etiquetas_script_generadas(): void
    {
        // Vite::useCspNonce() se fija una sola vez al arrancar la aplicación
        // (AppServiceProvider::boot), así que dentro de un mismo proceso el
        // nonce es estable entre peticiones — en producción cada petición
        // HTTP arranca su propia instancia y sí obtiene uno distinto. Lo que
        // debe verificarse aquí es que la cabecera y las etiquetas <script>
        // que emite @vite/@livewireScripts usen exactamente el mismo valor.
        $respuesta = $this->get(route('inicio'));

        preg_match("/'nonce-([^']+)'/", $respuesta->headers->get('Content-Security-Policy'), $coincidencia);
        $nonce = $coincidencia[1] ?? null;

        $this->assertNotEmpty($nonce);
        $respuesta->assertSee('nonce="'.$nonce.'"', false);
    }

    public function test_hsts_no_se_emite_sobre_una_conexion_http_sin_cifrar(): void
    {
        $respuesta = $this->get(route('inicio'));

        $respuesta->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_hsts_se_emite_cuando_la_peticion_llega_por_https(): void
    {
        // Request::create() marca la petición como segura a partir del
        // esquema "https" de la URL, igual que lo haría un balanceador que
        // termina TLS antes de reenviar a la aplicación.
        $respuesta = $this->get(str_replace('http://', 'https://', route('inicio')));

        $respuesta->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
}
