<?php

namespace App\Livewire\Publico;

use App\Models\Estrado;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Estrados digitales de la DRACS, sin autenticación (Fase 7): listado
 * numerado y consultable, con buscador por número, expediente o asunto.
 * Solo muestra estrados activos (Estrado::scopePublicado).
 */
class ListaEstradosPublica extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $busqueda = '';

    public function updating($propiedad): void
    {
        if ($propiedad === 'busqueda') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $estrados = Estrado::query()
            ->publicado()
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->buscar($this->busqueda))
            ->orderByDesc('numero')
            ->paginate(15);

        return view('livewire.publico.lista-estrados-publica', [
            'estrados' => $estrados,
        ]);
    }
}
