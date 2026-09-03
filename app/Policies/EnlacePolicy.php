<?php

namespace App\Policies;

use App\Models\Enlace;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización del directorio de enlaces (Fase 6). El directorio público
 * (RF-ENL-002) no pasa por esta policy: solo protege el panel administrativo.
 */
class EnlacePolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }

    public function view(User $user, Enlace $enlace): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Enlace $enlace): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Enlace $enlace): bool
    {
        return $this->viewAny($user);
    }
}
