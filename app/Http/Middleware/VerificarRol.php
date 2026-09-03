<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe una ruta a uno o más roles administrativos (RF-USR-002).
 * Uso: ->middleware('rol:administrador') o ->middleware('rol:administrador,administrador_contenido')
 */
class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        // Sin sesión: el middleware "auth" ya debería haber actuado antes,
        // pero se valida aquí también por seguridad ante cambios de orden.
        if (! $usuario) {
            abort(401);
        }

        if (! $usuario->activo || ! in_array($usuario->rol?->clave, $roles, strict: true)) {
            abort(403, 'No tienes permiso para acceder a este módulo.');
        }

        return $next($request);
    }
}
