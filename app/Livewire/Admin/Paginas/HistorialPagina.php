<?php

namespace App\Livewire\Admin\Paginas;

use App\Models\BitacoraAuditoria;
use App\Models\PaginaInstitucional;
use App\Models\PaginaInstitucionalVersion;
use App\Services\VersionadorPaginas;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Historial de versiones de una página institucional (editor de bloques
 * versionados, Fase 7): permite consultar el contenido archivado en cada
 * guardado anterior y restaurarlo como vigente.
 */
class HistorialPagina extends Component
{
    public ?int $paginaId = null;

    public string $tituloPagina = '';

    #[On('pagina:historial')]
    public function abrir(int $id): void
    {
        $this->paginaId = $id;

        try {
            $pagina = PaginaInstitucional::query()->findOrFail($id);
            $this->tituloPagina = $pagina->titulo;

            $this->dispatch('abrir-modal', nombre: 'historial-pagina');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar el historial de la página institucional.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar el historial de esta página.');
        }
    }

    public function restaurar(int $versionId, VersionadorPaginas $versionador): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        try {
            $version = PaginaInstitucionalVersion::query()->findOrFail($versionId);
            $pagina = PaginaInstitucional::query()->findOrFail($this->paginaId);
            $usuario = auth()->user();

            $versionador->restaurar($pagina, $version, $usuario);

            BitacoraAuditoria::registrar('pagina_institucional_version_restaurada', 'PaginaInstitucional', $pagina->id, [
                'titulo' => $pagina->titulo,
                'numero_version_restaurada' => $version->numero_version,
            ]);

            $this->dispatch('pagina-restaurada');
            $this->dispatch('mostrar-exito', mensaje: 'Se restauró la versión seleccionada.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al restaurar una versión de página institucional.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al restaurar esta versión.');
        }
    }

    public function render()
    {
        $versiones = $this->paginaId
            ? PaginaInstitucionalVersion::query()
                ->where('pagina_institucional_id', $this->paginaId)
                ->with('actualizadoPor')
                ->orderByDesc('numero_version')
                ->get()
            : collect();

        return view('livewire.admin.paginas.historial-pagina', [
            'versiones' => $versiones,
        ]);
    }
}
