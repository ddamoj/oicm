<?php

namespace App\Livewire\Admin\Noticias;

use App\Models\BitacoraAuditoria;
use App\Models\Noticia;
use App\Services\AlmacenImagenNoticia;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado y administración de noticias, avisos y comunicados (RF-NOT-001/002).
 * Autorizado a los roles "administrador" y "administrador_contenido" desde la
 * ruta (middleware `rol`) y reforzado aquí con la compuerta `gestionar-contenido`.
 */
class ListaNoticias extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    protected $queryString = ['busqueda', 'filtroEstado'];

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroEstado'], true)) {
            $this->resetPage();
        }
    }

    public function nuevo(): void
    {
        $this->dispatch('noticia:nuevo');
    }

    public function editar(int $id): void
    {
        $this->dispatch('noticia:editar', id: $id);
    }

    /**
     * Publica de inmediato o regresa a borrador, sin pasar por el formulario.
     */
    public function alternarEstatus(int $id): void
    {
        try {
            $noticia = Noticia::query()->findOrFail($id);

            $this->authorize('update', $noticia);

            if ($noticia->estatus === 'publicada') {
                $noticia->update(['estatus' => 'borrador']);
                BitacoraAuditoria::registrar('noticia_despublicada', 'Noticia', $noticia->id, ['titulo' => $noticia->titulo]);
                $this->dispatch('mostrar-exito', mensaje: 'Noticia regresada a borrador.');
            } else {
                $noticia->update(['estatus' => 'publicada', 'publicado_en' => $noticia->publicado_en ?? now()]);
                BitacoraAuditoria::registrar('noticia_publicada', 'Noticia', $noticia->id, ['titulo' => $noticia->titulo]);
                $this->dispatch('mostrar-exito', mensaje: 'Noticia publicada.');
            }
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar el estatus de la noticia.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar la noticia.');
        }
    }

    public function eliminar(int $id): void
    {
        try {
            $noticia = Noticia::query()->findOrFail($id);

            $this->authorize('delete', $noticia);

            app(AlmacenImagenNoticia::class)->eliminarArchivos($noticia);

            BitacoraAuditoria::registrar('noticia_eliminada', 'Noticia', $noticia->id, ['titulo' => $noticia->titulo]);

            $noticia->delete();

            $this->dispatch('mostrar-exito', mensaje: 'Noticia eliminada correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar la noticia.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar la noticia.');
        }
    }

    /**
     * Sin cuerpo: recibir el evento basta para que Livewire vuelva a
     * renderizar este componente con la lista actualizada tras guardar.
     */
    #[On('noticia-guardada')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', Noticia::class);

        $noticias = Noticia::query()
            ->with('autor')
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->buscar($this->busqueda))
            ->when($this->filtroEstado !== '', fn ($consulta) => $consulta->where('estatus', $this->filtroEstado))
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('livewire.admin.noticias.lista-noticias', [
            'noticias' => $noticias,
        ]);
    }
}
