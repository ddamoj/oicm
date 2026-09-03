<?php

namespace App\Livewire\Admin\Contactos;

use App\Models\BitacoraAuditoria;
use App\Models\Contacto;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Administración del directorio de contacto (Fase 8): domicilio, teléfono,
 * horario, mapa y canal de quejas y denuncias por Dirección. Directorio
 * corto (una fila por Dirección + contacto general), sin paginación.
 */
class ListaContactos extends Component
{
    public function nuevo(): void
    {
        $this->dispatch('contacto:nuevo');
    }

    public function editar(int $id): void
    {
        $this->dispatch('contacto:editar', id: $id);
    }

    public function eliminar(int $id): void
    {
        try {
            $contacto = Contacto::query()->findOrFail($id);

            $this->authorize('delete', $contacto);

            BitacoraAuditoria::registrar('contacto_eliminado', 'Contacto', $contacto->id, [
                'nombre_area' => $contacto->nombre_area,
            ]);

            $contacto->delete();

            $this->dispatch('mostrar-exito', mensaje: 'Contacto eliminado correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar el contacto.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar el contacto.');
        }
    }

    #[On('contacto-guardado')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', Contacto::class);

        $contactos = Contacto::query()->with('direccion')->ordenado()->get();

        return view('livewire.admin.contactos.lista-contactos', ['contactos' => $contactos]);
    }
}
