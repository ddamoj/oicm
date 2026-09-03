<?php

namespace App\Livewire\Publico;

use App\Models\Normatividad;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Consulta pública del marco normativo, sin autenticación (Fase 7): filtro
 * por ámbito (Federal | Estatal | Municipal) y buscador por título. Solo
 * muestra ordenamientos vigentes (Normatividad::scopePublicado).
 */
class ListaNormatividadPublica extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $busqueda = '';

    #[Url(as: 'ambito')]
    public string $ambito = '';

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'ambito'], true)) {
            $this->resetPage();
        }
    }

    public function filtrarAmbito(string $ambito): void
    {
        $this->ambito = $this->ambito === $ambito ? '' : $ambito;
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'ambito']);
        $this->resetPage();
    }

    public function render()
    {
        $normatividad = Normatividad::query()
            ->publicado()
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->where('titulo', 'like', "%{$this->busqueda}%"))
            ->when($this->ambito !== '', fn ($consulta) => $consulta->porAmbito($this->ambito))
            ->paginate(10);

        return view('livewire.publico.lista-normatividad-publica', [
            'normatividad' => $normatividad,
        ]);
    }
}
