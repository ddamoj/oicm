<?php

namespace App\Livewire\Admin\Galerias;

use App\Models\BitacoraAuditoria;
use App\Models\Galeria;
use App\Services\AlmacenMedioGaleria;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado y administración de galerías de fotos y videos por evento
 * (Fase 8). Autorizado a los roles "administrador" y
 * "administrador_contenido" desde la ruta (middleware `rol`) y reforzado aquí
 * con la compuerta `gestionar-contenido`.
 */
class ListaGalerias extends Component
{
    use WithPagination;

    public string $busqueda = '';

    protected $queryString = ['busqueda'];

    public function updating($propiedad): void
    {
        if ($propiedad === 'busqueda') {
            $this->resetPage();
        }
    }

    public function nueva(): void
    {
        $this->dispatch('galeria:nueva');
    }

    public function editar(int $id): void
    {
        $this->dispatch('galeria:editar', id: $id);
    }

    /**
     * Baja lógica: retira la galería del grid público de inmediato (reversible).
     */
    public function alternarPublicada(int $id): void
    {
        try {
            $galeria = Galeria::query()->findOrFail($id);

            $this->authorize('update', $galeria);

            $galeria->update(['publicada' => ! $galeria->publicada]);

            BitacoraAuditoria::registrar($galeria->publicada ? 'galeria_publicada' : 'galeria_despublicada', 'Galeria', $galeria->id, [
                'titulo' => $galeria->titulo,
            ]);

            $this->dispatch('mostrar-exito', mensaje: $galeria->publicada ? 'Galería visible en el grid público.' : 'Galería retirada del grid público.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar la visibilidad de la galería.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar la galería.');
        }
    }

    /**
     * Baja definitiva: elimina también los archivos físicos de todos los
     * medios de la galería antes del borrado lógico del registro.
     */
    public function eliminar(int $id): void
    {
        try {
            $galeria = Galeria::query()->with('medios')->findOrFail($id);

            $this->authorize('delete', $galeria);

            $almacen = app(AlmacenMedioGaleria::class);
            foreach ($galeria->medios as $medio) {
                $almacen->eliminarArchivos($medio);
            }

            BitacoraAuditoria::registrar('galeria_eliminada', 'Galeria', $galeria->id, [
                'titulo' => $galeria->titulo,
            ]);

            $galeria->delete();

            $this->dispatch('mostrar-exito', mensaje: 'Galería eliminada correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar la galería.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar la galería.');
        }
    }

    #[On('galeria-guardada')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', Galeria::class);

        $galerias = Galeria::query()
            ->withCount('medios')
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->where('titulo', 'like', "%{$this->busqueda}%"))
            ->orderByDesc('fecha_evento')
            ->paginate(10);

        return view('livewire.admin.galerias.lista-galerias', ['galerias' => $galerias]);
    }
}
