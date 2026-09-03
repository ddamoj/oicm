<?php

namespace App\Livewire\Admin\Paginas;

use App\Models\BitacoraAuditoria;
use App\Models\Direccion;
use App\Models\PaginaInstitucional;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado y administración del contenido institucional (Fase 7): "Quiénes
 * somos" y las páginas propias de cada Dirección. Autorizado a los roles
 * "administrador" y "administrador_contenido" desde la ruta (middleware
 * `rol`) y reforzado aquí con la compuerta `gestionar-contenido`.
 */
class ListaPaginasInstitucionales extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstatus = '';

    protected $queryString = ['busqueda', 'filtroEstatus'];

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroEstatus'], true)) {
            $this->resetPage();
        }
    }

    public function nuevo(): void
    {
        $this->dispatch('pagina:nuevo');
    }

    public function editar(int $id): void
    {
        $this->dispatch('pagina:editar', id: $id);
    }

    public function verHistorial(int $id): void
    {
        $this->dispatch('pagina:historial', id: $id);
    }

    /**
     * Baja lógica: la página deja de mostrarse en el sitio público de
     * inmediato (scopePublicado) sin perder el contenido para poder
     * reactivarla.
     */
    public function despublicar(int $id): void
    {
        try {
            $pagina = PaginaInstitucional::query()->findOrFail($id);

            $this->authorize('update', $pagina);

            $pagina->update(['estatus' => 'borrador']);

            BitacoraAuditoria::registrar('pagina_institucional_despublicada', 'PaginaInstitucional', $pagina->id, [
                'titulo' => $pagina->titulo,
            ]);

            $this->dispatch('mostrar-exito', mensaje: 'Página retirada del sitio público.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al despublicar la página institucional.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar la página.');
        }
    }

    /**
     * Sin cuerpo: recibir el evento basta para que Livewire vuelva a
     * renderizar este componente con la lista actualizada tras guardar.
     */
    #[On('pagina-guardada')]
    public function alGuardar(): void {}

    #[On('pagina-restaurada')]
    public function alRestaurar(): void {}

    public function render()
    {
        $this->authorize('viewAny', PaginaInstitucional::class);

        $paginas = PaginaInstitucional::query()
            ->with('direccion')
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->where('titulo', 'like', "%{$this->busqueda}%"))
            ->when($this->filtroEstatus !== '', fn ($consulta) => $consulta->where('estatus', $this->filtroEstatus))
            ->orderBy('titulo')
            ->paginate(10);

        return view('livewire.admin.paginas.lista-paginas-institucionales', [
            'paginas' => $paginas,
            'direcciones' => Direccion::query()->orderBy('orden')->get(),
        ]);
    }
}
