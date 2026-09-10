<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización del tablero de estadísticas de visitas.
 *
 * Se abre a los dos roles con `gestionar-contenido`: saber qué secciones se
 * consultan es información de trabajo para quien publica, no un dato
 * sensible de administración. El tablero es además de solo lectura y no
 * expone ningún dato personal.
 */
class VisitaPaginaPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }
}
