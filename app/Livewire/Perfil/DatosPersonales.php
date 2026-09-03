<?php

namespace App\Livewire\Perfil;

use App\Models\BitacoraAuditoria;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;
use Throwable;

/**
 * Perfil propio: nombre y correo. Reutiliza la acción de dominio de Fortify
 * (`UpdateUserProfileInformation`) para no duplicar su validación.
 */
class DatosPersonales extends Component
{
    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    public function guardar(UpdatesUserProfileInformation $accion): void
    {
        try {
            $accion->update(auth()->user(), [
                'name' => $this->name,
                'email' => $this->email,
            ]);

            BitacoraAuditoria::registrar('perfil_actualizado', 'User', auth()->id());

            $this->dispatch('mostrar-exito', mensaje: 'Tus datos se actualizaron correctamente.');
        } catch (ValidationException $excepcion) {
            // Se deja propagar: Livewire la traduce en errores de validación del formulario.
            throw $excepcion;
        } catch (Throwable $excepcion) {
            Log::error('Error al actualizar el perfil.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar tus datos.');
        }
    }

    public function render()
    {
        return view('livewire.perfil.datos-personales');
    }
}
