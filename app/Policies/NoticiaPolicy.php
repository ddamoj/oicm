<?php

namespace App\Policies;

use App\Models\Noticia;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización del panel de noticias (Fase 5). La consulta pública
 * (RF-NOT-002) no pasa por esta policy: solo protege el panel administrativo.
 */
class NoticiaPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }

    public function view(User $user, Noticia $noticia): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Noticia $noticia): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Noticia $noticia): bool
    {
        return $this->viewAny($user);
    }
}
