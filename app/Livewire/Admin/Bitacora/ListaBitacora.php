<?php

namespace App\Livewire\Admin\Bitacora;

use App\Models\BitacoraAuditoria;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Consulta de la bitácora de auditoría (quién, qué, cuándo, IP). Solo lectura:
 * ni se edita ni se borra, por diseño. Restringida al rol "administrador"
 * desde la ruta (middleware `rol`).
 */
class ListaBitacora extends Component
{
    use WithPagination;

    public string $filtroAccion = '';

    public string $filtroUsuario = '';

    public string $desde = '';

    public string $hasta = '';

    protected $queryString = ['filtroAccion', 'filtroUsuario', 'desde', 'hasta'];

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['filtroAccion', 'filtroUsuario', 'desde', 'hasta'], true)) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function acciones(): Collection
    {
        return BitacoraAuditoria::query()->select('accion')->distinct()->orderBy('accion')->pluck('accion');
    }

    #[Computed]
    public function usuarios(): Collection
    {
        return User::query()->orderBy('name')->get(['id', 'name']);
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        $registros = BitacoraAuditoria::query()
            ->with('usuario')
            ->when($this->filtroAccion !== '', fn ($consulta) => $consulta->where('accion', $this->filtroAccion))
            ->when($this->filtroUsuario !== '', fn ($consulta) => $consulta->where('user_id', $this->filtroUsuario))
            ->when($this->desde !== '', fn ($consulta) => $consulta->whereDate('creado_en', '>=', $this->desde))
            ->when($this->hasta !== '', fn ($consulta) => $consulta->whereDate('creado_en', '<=', $this->hasta))
            ->orderByDesc('creado_en')
            ->paginate(20);

        return view('livewire.admin.bitacora.lista-bitacora', ['registros' => $registros]);
    }
}
