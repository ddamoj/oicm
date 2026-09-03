<?php

namespace App\Policies;

use App\Models\Normatividad;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización del marco normativo (Fase 7). La consulta pública por ámbito
 * no pasa por esta policy: solo protege el panel administrativo.
 */
class NormatividadPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }

    public function view(User $user, Normatividad $normatividad): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Normatividad $normatividad): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Normatividad $normatividad): bool
    {
        return $this->viewAny($user);
    }
}
