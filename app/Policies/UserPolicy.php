<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Solo el rol "administrador", con cuenta activa, gestiona usuarios (RF-USR-001).
     */
    public function viewAny(User $user): bool
    {
        return $user->activo && $user->tieneRol('administrador');
    }

    public function view(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Baja (softDelete). Nadie puede eliminarse a sí mismo ni dejar el sistema
     * sin administradores activos.
     */
    public function delete(User $user, User $model): bool
    {
        return $this->viewAny($user)
            && $user->id !== $model->id
            && ! $this->esUltimoAdministradorActivo($model);
    }

    /**
     * Desactivación (reversible). Misma restricción que la baja definitiva:
     * nadie puede desactivarse a sí mismo ni dejar el sistema sin
     * administradores activos, porque revoca el acceso de inmediato.
     */
    public function desactivar(User $user, User $model): bool
    {
        return $this->viewAny($user)
            && $user->id !== $model->id
            && ! $this->esUltimoAdministradorActivo($model);
    }

    /**
     * Reactivación de una cuenta previamente desactivada.
     */
    public function activar(User $user, User $model): bool
    {
        return $this->viewAny($user) && $user->id !== $model->id;
    }

    /**
     * True cuando $model es un administrador activo y no queda ningún otro
     * administrador activo en el sistema — protege contra quedarse sin acceso
     * administrativo al desactivar o eliminar la última cuenta con ese rol.
     */
    private function esUltimoAdministradorActivo(User $model): bool
    {
        if (! $model->activo || ! $model->tieneRol('administrador')) {
            return false;
        }

        return User::query()
            ->where('activo', true)
            ->whereHas('rol', fn ($consulta) => $consulta->where('clave', 'administrador'))
            ->where('id', '!=', $model->id)
            ->doesntExist();
    }
}
