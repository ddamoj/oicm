<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

/**
 * Política de contraseña robusta del OICM (Fase 3, RF-USR-002): mínimo 10
 * caracteres, mayúsculas, minúsculas, números y símbolos. Punto único de
 * definición, reutilizado por Fortify (login/perfil) y por el CRUD de
 * usuarios, para no repetir ni desalinear la regla entre ambos flujos.
 */
class ReglasContrasena
{
    /**
     * @return array<int, Password|string>
     */
    public static function reglas(): array
    {
        $regla = Password::min(10)->mixedCase()->numbers()->symbols();

        // `uncompromised()` consulta la API de HaveIBeenPwned; se omite en pruebas
        // para no depender de red externa durante la suite.
        if (! app()->environment('testing')) {
            $regla = $regla->uncompromised();
        }

        return [$regla];
    }
}
