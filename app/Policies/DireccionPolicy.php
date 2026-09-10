<?php

namespace App\Policies;

use App\Models\Direccion;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Autorización del catálogo de Direcciones del OICM.
 *
 * A diferencia de noticias o documentos, las Direcciones son estructura
 * institucional: clasifican documentos, páginas y contactos, y su `clave`
 * forma parte de las rutas públicas (`/direcciones/{clave}`). Por eso se
 * reservan al rol "administrador" mediante `gestionar-configuracion`, en la
 * misma línea que el resto de catálogos del panel.
 *
 * El organigrama público de "Quiénes somos" no pasa por esta policy: solo
 * protege el panel administrativo.
 */
class DireccionPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('gestionar-configuracion');
    }

    public function view(User $user, Direccion $direccion): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Direccion $direccion): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Direccion $direccion): bool
    {
        return $this->viewAny($user);
    }
}
