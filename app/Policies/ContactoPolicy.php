<?php

namespace App\Policies;

use App\Models\Contacto;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización del directorio de contacto (Fase 8). La página pública no
 * pasa por esta policy: solo protege el panel administrativo.
 */
class ContactoPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }

    public function view(User $user, Contacto $contacto): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Contacto $contacto): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Contacto $contacto): bool
    {
        return $this->viewAny($user);
    }
}
