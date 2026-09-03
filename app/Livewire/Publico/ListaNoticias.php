<?php

namespace App\Livewire\Publico;

use App\Models\Noticia;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listado público de noticias, sin autenticación (RF-NOT-002/003): buscador
 * por palabra clave y rango de fechas, orden de la más reciente a la más
 * antigua. Solo muestra noticias publicadas y con fecha ya cumplida
 * (Noticia::scopePublicado).
 */
class ListaNoticias extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $busqueda = '';

    #[Url(as: 'desde')]
    public string $desde = '';

    #[Url(as: 'hasta')]
    public string $hasta = '';

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'desde', 'hasta'], true)) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'desde', 'hasta']);
        $this->resetPage();
    }

    public function render()
    {
        $noticias = Noticia::query()
            ->publicado()
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->buscar($this->busqueda))
            ->when(
                $this->desde !== '' || $this->hasta !== '',
                fn ($consulta) => $consulta->entreFechas($this->desde ?: null, $this->hasta ?: null)
            )
            ->paginate(9);

        return view('livewire.publico.lista-noticias', [
            'noticias' => $noticias,
        ]);
    }
}
