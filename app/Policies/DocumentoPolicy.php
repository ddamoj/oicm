<?php

namespace App\Policies;

use App\Models\Documento;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización del repositorio de documentos (Fase 4). La consulta pública
 * (RF-DES-001) no pasa por esta policy: solo protege el panel administrativo.
 */
class DocumentoPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-contenido');
    }

    public function view(User $user, Documento $documento): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Documento $documento): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Documento $documento): bool
    {
        return $this->viewAny($user);
    }
}
