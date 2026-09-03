<?php

namespace App\Livewire\Admin\Enlaces;

use App\Models\BitacoraAuditoria;
use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado y administración del directorio de enlaces de interés
 * (RF-ENL-001/002). Autorizado a los roles "administrador" y
 * "administrador_contenido" desde la ruta (middleware `rol`) y reforzado aquí
 * con la compuerta `gestionar-contenido`.
 */
class ListaEnlaces extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroCategoria = '';

    public string $filtroEstado = '';

    protected $queryString = ['busqueda', 'filtroCategoria', 'filtroEstado'];

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroCategoria', 'filtroEstado'], true)) {
            $this->resetPage();
        }
    }

    public function nuevo(): void
    {
        $this->dispatch('enlace:nuevo');
    }

    public function editar(int $id): void
    {
        $this->dispatch('enlace:editar', id: $id);
    }

    /**
     * Baja lógica: retira el enlace de la vista pública de inmediato
     * (RF-ENL-001) sin perder el registro para poder reactivarlo.
     */
    public function alternarActivo(int $id): void
    {
        try {
            $enlace = Enlace::query()->findOrFail($id);

            $this->authorize('update', $enlace);

            $enlace->update(['activo' => ! $enlace->activo]);

            BitacoraAuditoria::registrar($enlace->activo ? 'enlace_activado' : 'enlace_desactivado', 'Enlace', $enlace->id, [
                'nombre' => $enlace->nombre,
            ]);

            $this->dispatch('mostrar-exito', mensaje: $enlace->activo ? 'Enlace visible en el directorio público.' : 'Enlace retirado del directorio público.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar la visibilidad del enlace.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar el enlace.');
        }
    }

    /**
     * Baja definitiva (borrado lógico): el `scopePublicado` del modelo ya
     * excluye los enlaces eliminados, así que desaparece de inmediato del
     * directorio público.
     */
    public function eliminar(int $id): void
    {
        try {
            $enlace = Enlace::query()->findOrFail($id);

            $this->authorize('delete', $enlace);

            BitacoraAuditoria::registrar('enlace_eliminado', 'Enlace', $enlace->id, [
                'nombre' => $enlace->nombre,
            ]);

            $enlace->delete();

            $this->dispatch('mostrar-exito', mensaje: 'Enlace eliminado correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar el enlace.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar el enlace.');
        }
    }

    /**
     * Sin cuerpo: recibir el evento basta para que Livewire vuelva a
     * renderizar este componente con la lista actualizada tras guardar.
     */
    #[On('enlace-guardado')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', Enlace::class);

        $enlaces = Enlace::query()
            ->with('categoria')
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->buscar($this->busqueda))
            ->when($this->filtroCategoria !== '', fn ($consulta) => $consulta->porCategoria((int) $this->filtroCategoria))
            ->when($this->filtroEstado !== '', fn ($consulta) => $consulta->where('activo', $this->filtroEstado === 'activo'))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.admin.enlaces.lista-enlaces', [
            'enlaces' => $enlaces,
            'categorias' => CategoriaEnlace::query()->orderBy('orden')->get(),
        ]);
    }
}
