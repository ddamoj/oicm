<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeceras de seguridad HTTP (Fase 9): endurece toda respuesta del grupo
 * "web" contra clickjacking, sniffing de MIME, fuga de referer y, sobre
 * todo, ejecución de script/estilo/embed no autorizado (CSP). Se aplica de
 * forma global para no depender de que cada ruta nueva recuerde declararla.
 *
 * La política se calcula a partir de un inventario real del código (ver
 * bitácora de la Fase 9 en context/plandetrabajo.md): no hay ni un solo
 * script o estilo externo, ni fetch/XHR a otro origen; los únicos orígenes
 * externos legítimos son los tres iframes de /contacto (mapa) y de la
 * galería (YouTube/Vimeo, siempre construidos en servidor a partir de una
 * lista blanca en App\Models\GaleriaMedio, nunca de la URL cruda).
 */
class CabecerasSeguridad
{
    public function handle(Request $request, Closure $next): Response
    {
        // Nonce único por petición para los <script> propios (Vite/Livewire lo
        // adjuntan solos vía Vite::useCspNonce(), fijado en AppServiceProvider).
        $nonce = app(Vite::class)->cspNonce() ?? Str::random(32);

        $respuesta = $next($request);

        try {
            $this->aplicarCabeceras($respuesta, $request, $nonce);
        } catch (\Throwable $excepcion) {
            // Un fallo al calcular la política nunca debe tumbar la respuesta:
            // se deja pasar sin cabeceras antes que devolver un error 500.
            report($excepcion);
        }

        return $respuesta;
    }

    /**
     * Añade las cabeceras a la respuesta ya construida.
     */
    private function aplicarCabeceras(Response $respuesta, Request $request, string $nonce): void
    {
        $respuesta->headers->set('X-Content-Type-Options', 'nosniff');
        $respuesta->headers->set('X-Frame-Options', 'DENY');
        $respuesta->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $respuesta->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=(), payment=(), usb=()');

        // HSTS solo tiene sentido sobre una conexión ya cifrada: emitirla en
        // HTTP plano (como el entorno local sin TLS) no protege nada y puede
        // confundir a un cliente que nunca llegó a negociar HTTPS.
        if ($request->secure()) {
            $respuesta->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $respuesta->headers->set('Content-Security-Policy', $this->politica($nonce));
    }

    /**
     * Construye la Content-Security-Policy. 'unsafe-eval' es obligado porque
     * Alpine (empaquetado dentro del bundle de Livewire 3) evalúa las
     * expresiones x-* con new Function, y Livewire no publica un build
     * CSP-safe; 'unsafe-inline' en style-src es obligado porque SweetAlert2
     * y Quill inyectan estilos en tiempo de ejecución. En desarrollo se abre
     * el origen del servidor Vite para no romper el HMR de `npm run dev`.
     */
    private function politica(string $nonce): string
    {
        $scriptSrc = "'self' 'nonce-{$nonce}' 'unsafe-eval'";
        $connectSrc = "'self'";

        if (app()->isLocal()) {
            $scriptSrc .= ' http://localhost:5173';
            $connectSrc .= ' http://localhost:5173 ws://localhost:5173';
        }

        $directivas = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "script-src {$scriptSrc}",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob:",
            "media-src 'self'",
            "font-src 'self'",
            "connect-src {$connectSrc}",
            'frame-src https://www.google.com https://www.youtube-nocookie.com https://player.vimeo.com',
        ];

        return implode('; ', $directivas);
    }
}
