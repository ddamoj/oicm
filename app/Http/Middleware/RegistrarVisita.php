<?php

namespace App\Http\Middleware;

use App\Models\VisitaPagina;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contabiliza las visitas a las páginas públicas del micrositio.
 *
 * Va aplicado a todo el grupo `web` —igual que [CabecerasSeguridad]— en vez
 * de ruta por ruta, para que una sección pública nueva quede contabilizada
 * sin tener que acordarse de declararlo. Lo que no debe contarse se descarta
 * aquí mismo.
 */
class RegistrarVisita
{
    /**
     * Prefijos que nunca representan la visita de una persona a una página:
     * el panel administrativo, el flujo de autenticación, las peticiones
     * internas de Livewire (que dispararían una "visita" por cada tecla) y
     * las descargas de archivos, que ya llevan su propio contador.
     *
     * @var list<string>
     */
    private const RUTAS_EXCLUIDAS = [
        'admin',
        'admin/*',
        'livewire/*',
        'login',
        'logout',
        'forgot-password',
        'reset-password/*',
        'user/*',
        'up',
        '*/descargar',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $respuesta = $next($request);

        if ($this->debeContarse($request, $respuesta)) {
            VisitaPagina::registrar($request, $request->path());
        }

        return $respuesta;
    }

    /**
     * Solo cuentan las cargas de página completas que terminaron bien: un GET
     * normal con respuesta 200 y HTML. Se excluyen además las peticiones
     * AJAX, que corresponden a interacciones dentro de una página ya contada.
     */
    private function debeContarse(Request $request, Response $respuesta): bool
    {
        if (! $request->isMethod('GET') || $respuesta->getStatusCode() !== 200) {
            return false;
        }

        if ($request->ajax() || $request->isJson() || $request->expectsJson()) {
            return false;
        }

        if ($request->is(...self::RUTAS_EXCLUIDAS)) {
            return false;
        }

        return str_contains((string) $respuesta->headers->get('Content-Type'), 'text/html');
    }
}
