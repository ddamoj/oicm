<?php

namespace App\Livewire\Admin\Estrados;

use App\Models\BitacoraAuditoria;
use App\Models\Estrado;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado y administración de los estrados digitales de la DRACS (Fase 7).
 * Autorizado a los roles "administrador" y "administrador_contenido" desde
 * la ruta (middleware `rol`) y reforzado aquí con la compuerta
 * `gestionar-contenido`.
 */
class ListaEstrados extends Component
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

    public function nuevo(): void
    {
        $this->dispatch('estrado:nuevo');
    }

    /**
     * Baja lógica: retira la notificación del listado público de inmediato
     * sin perder el registro ni el archivo, para conservar la constancia.
     */
    public function alternarActivo(int $id): void
    {
        try {
            $estrado = Estrado::query()->findOrFail($id);

            $this->authorize('update', $estrado);

            $estrado->update(['activo' => ! $estrado->activo]);

            BitacoraAuditoria::registrar($estrado->activo ? 'estrado_activado' : 'estrado_desactivado', 'Estrado', $estrado->id, [
                'numero' => $estrado->numero,
            ]);

            $this->dispatch('mostrar-exito', mensaje: $estrado->activo ? 'Estrado visible en el listado público.' : 'Estrado retirado del listado público.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar la visibilidad del estrado.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar el estrado.');
        }
    }

    #[On('estrado-guardado')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', Estrado::class);

        $estrados = Estrado::query()
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->buscar($this->busqueda))
            ->orderByDesc('numero')
            ->paginate(15);

        return view('livewire.admin.estrados.lista-estrados', [
            'estrados' => $estrados,
        ]);
    }
}
