<?php

namespace App\Livewire\Perfil;

use App\Models\BitacoraAuditoria;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Livewire\Component;
use Throwable;

/**
 * Cambio de la propia contraseña, con política robusta (RF-USR-002). Reutiliza
 * la acción de dominio de Fortify (`UpdateUserPassword`), que ya exige la
 * contraseña actual y aplica `ReglasContrasena` a la nueva.
 */
class CambiarContrasena extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function guardar(UpdatesUserPasswords $accion): void
    {
        try {
            $accion->update(auth()->user(), [
                'current_password' => $this->current_password,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);

            BitacoraAuditoria::registrar('contrasena_cambiada', 'User', auth()->id());

            $this->reset(['current_password', 'password', 'password_confirmation']);
            $this->dispatch('mostrar-exito', mensaje: 'Tu contraseña se actualizó correctamente.');
        } catch (ValidationException $excepcion) {
            // Se deja propagar: Livewire la traduce en errores de validación del formulario.
            throw $excepcion;
        } catch (Throwable $excepcion) {
            Log::error('Error al cambiar la contraseña.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al cambiar tu contraseña.');
        }
    }

    public function render()
    {
        return view('livewire.perfil.cambiar-contrasena');
    }
}
