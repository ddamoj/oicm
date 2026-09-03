<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cierra de inmediato cualquier sesión de una cuenta desactivada (RF-USR-001:
 * "la baja revoca el acceso de inmediato"). Complementa el borrado directo de
 * filas en `sessions` que hace el CRUD de usuarios: esta comprobación actúa
 * como respaldo para sesiones que aún no hayan sido invalidadas por esa vía.
 */
class VerificarCuentaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && ! $usuario->activo) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('status', 'Tu cuenta ha sido desactivada. Contacta a un administrador si crees que es un error.');
        }

        return $next($request);
    }
}
