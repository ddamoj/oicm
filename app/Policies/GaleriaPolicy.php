<?php

namespace App\Policies;

use App\Models\Galeria;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización de la galería de fotos y videos (Fase 8). El grid público no
 * pasa por esta policy: solo protege el panel administrativo.
 */
class GaleriaPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }

    public function view(User $user, Galeria $galeria): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Galeria $galeria): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Galeria $galeria): bool
    {
        return $this->viewAny($user);
    }
}
