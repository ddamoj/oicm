<?php

namespace App\Livewire\Admin\Documentos;

use App\Models\BitacoraAuditoria;
use App\Models\CategoriaDocumento;
use App\Models\Documento;
use App\Services\AlmacenDocumentos;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado y administración del repositorio de documentos (RF-CAR-001/002/003).
 * Autorizado a los roles "administrador" y "administrador_contenido" desde la
 * ruta (middleware `rol`) y reforzado aquí con la compuerta `gestionar-contenido`.
 */
class ListaDocumentos extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroCategoria = '';

    public string $filtroEstado = '';

    /** Documento cuyo historial de versiones se muestra en el modal, si alguno. */
    public ?int $documentoVersionesId = null;

    protected $queryString = ['busqueda', 'filtroCategoria', 'filtroEstado'];

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroCategoria', 'filtroEstado'], true)) {
            $this->resetPage();
        }
    }

    public function nuevo(): void
    {
        $this->dispatch('documento:nuevo');
    }

    public function editar(int $id): void
    {
        $this->dispatch('documento:editar', id: $id);
    }

    public function reemplazarArchivo(int $id): void
    {
        $this->dispatch('documento:reemplazar', id: $id);
    }

    public function verVersiones(int $id): void
    {
        $this->documentoVersionesId = $id;
        $this->dispatch('abrir-modal', nombre: 'versiones-documento');
    }

    /**
     * Publica u oculta un documento de la vista pública sin eliminarlo.
     */
    public function alternarPublicado(int $id): void
    {
        try {
            $documento = Documento::query()->findOrFail($id);

            $this->authorize('update', $documento);

            $documento->update(['publicado' => ! $documento->publicado]);

            BitacoraAuditoria::registrar($documento->publicado ? 'documento_publicado' : 'documento_ocultado', 'Documento', $documento->id, [
                'nombre' => $documento->nombre,
            ]);

            $this->dispatch('mostrar-exito', mensaje: $documento->publicado ? 'Documento publicado.' : 'Documento ocultado de la vista pública.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar la visibilidad del documento.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar el documento.');
        }
    }

    /**
     * Baja definitiva: elimina también los archivos físicos (vigente e
     * históricos) para no dejar residuos en el disco privado.
     */
    public function eliminar(int $id): void
    {
        try {
            $documento = Documento::query()->with('versiones')->findOrFail($id);

            $this->authorize('delete', $documento);

            app(AlmacenDocumentos::class)->eliminarArchivos($documento);

            BitacoraAuditoria::registrar('documento_eliminado', 'Documento', $documento->id, [
                'nombre' => $documento->nombre,
            ]);

            $documento->delete();

            $this->dispatch('mostrar-exito', mensaje: 'Documento eliminado correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar el documento.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar el documento.');
        }
    }

    /**
     * Sin cuerpo: recibir el evento basta para que Livewire vuelva a
     * renderizar este componente con la lista actualizada tras guardar.
     */
    #[On('documento-guardado')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', Documento::class);

        $documentos = Documento::query()
            ->with(['categoria', 'direccion', 'versiones'])
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->buscar($this->busqueda))
            ->when($this->filtroCategoria !== '', fn ($consulta) => $consulta->porCategoria((int) $this->filtroCategoria))
            ->when($this->filtroEstado !== '', fn ($consulta) => $consulta->where('publicado', $this->filtroEstado === 'publicado'))
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('livewire.admin.documentos.lista-documentos', [
            'documentos' => $documentos,
            'categorias' => CategoriaDocumento::query()->orderBy('orden')->get(),
            'documentoVersiones' => $this->documentoVersionesId
                ? Documento::query()->with('versiones.reemplazadoPor')->find($this->documentoVersionesId)
                : null,
        ]);
    }
}
