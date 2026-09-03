<?php

namespace App\Policies;

use App\Models\PaginaInstitucional;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización del editor de contenido institucional (Fase 7). Las vistas
 * públicas no pasan por esta policy: solo protege el panel administrativo.
 */
class PaginaInstitucionalPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }

    public function view(User $user, PaginaInstitucional $pagina): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, PaginaInstitucional $pagina): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, PaginaInstitucional $pagina): bool
    {
        return $this->viewAny($user);
    }
}
