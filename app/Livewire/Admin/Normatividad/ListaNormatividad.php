<?php

namespace App\Livewire\Admin\Normatividad;

use App\Models\BitacoraAuditoria;
use App\Models\Normatividad;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado y administración del marco normativo (Fase 7): el Administrador de
 * Contenido mantiene actualizadas las leyes, reglamentos y lineamientos
 * federales, estatales y municipales (ERS §3). Autorizado a los roles
 * "administrador" y "administrador_contenido" desde la ruta (middleware
 * `rol`) y reforzado aquí con la compuerta `gestionar-contenido`.
 */
class ListaNormatividad extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroAmbito = '';

    protected $queryString = ['busqueda', 'filtroAmbito'];

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroAmbito'], true)) {
            $this->resetPage();
        }
    }

    public function nuevo(): void
    {
        $this->dispatch('normatividad:nuevo');
    }

    public function editar(int $id): void
    {
        $this->dispatch('normatividad:editar', id: $id);
    }

    /**
     * Baja lógica reversible: retira el ordenamiento de la consulta pública
     * de inmediato sin perder el registro (por ejemplo, mientras se revisa
     * una reforma antes de republicar la ficha).
     */
    public function alternarVigente(int $id): void
    {
        try {
            $normatividad = Normatividad::query()->findOrFail($id);

            $this->authorize('update', $normatividad);

            $normatividad->update(['vigente' => ! $normatividad->vigente]);

            BitacoraAuditoria::registrar($normatividad->vigente ? 'normatividad_activada' : 'normatividad_desactivada', 'Normatividad', $normatividad->id, [
                'titulo' => $normatividad->titulo,
            ]);

            $this->dispatch('mostrar-exito', mensaje: $normatividad->vigente ? 'Ordenamiento visible en la consulta pública.' : 'Ordenamiento retirado de la consulta pública.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar la vigencia del ordenamiento.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar el ordenamiento.');
        }
    }

    public function eliminar(int $id): void
    {
        try {
            $normatividad = Normatividad::query()->findOrFail($id);

            $this->authorize('delete', $normatividad);

            BitacoraAuditoria::registrar('normatividad_eliminada', 'Normatividad', $normatividad->id, [
                'titulo' => $normatividad->titulo,
            ]);

            $normatividad->delete();

            $this->dispatch('mostrar-exito', mensaje: 'Ordenamiento eliminado correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar el ordenamiento.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar el ordenamiento.');
        }
    }

    #[On('normatividad-guardada')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', Normatividad::class);

        $normatividad = Normatividad::query()
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->where('titulo', 'like', "%{$this->busqueda}%"))
            ->when($this->filtroAmbito !== '', fn ($consulta) => $consulta->porAmbito($this->filtroAmbito))
            ->orderBy('ambito')
            ->orderBy('orden')
            ->paginate(15);

        return view('livewire.admin.normatividad.lista-normatividad', [
            'normatividad' => $normatividad,
        ]);
    }
}
