<?php

namespace App\Policies;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización de los departamentos que integran cada Dirección (organigrama
 * de "Quiénes somos"). Comparte criterio con [DireccionPolicy]: es estructura
 * institucional, reservada al rol "administrador".
 */
class DepartamentoPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-configuracion');
    }

    public function view(User $user, Departamento $departamento): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Departamento $departamento): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Departamento $departamento): bool
    {
        return $this->viewAny($user);
    }
}
