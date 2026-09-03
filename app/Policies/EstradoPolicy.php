<?php

namespace App\Policies;

use App\Models\Estrado;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización de los estrados digitales de la DRACS (Fase 7). El listado
 * público no pasa por esta policy: solo protege el panel administrativo.
 */
class EstradoPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }

    public function view(User $user, Estrado $estrado): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Estrado $estrado): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Estrado $estrado): bool
    {
        return $this->viewAny($user);
    }
}
