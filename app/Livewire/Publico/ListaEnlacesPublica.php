<?php

namespace App\Livewire\Publico;

use App\Models\CategoriaEnlace;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Directorio público de enlaces de interés, sin autenticación (RF-ENL-002):
 * agrupado visualmente por categoría, con buscador opcional por nombre o
 * descripción. Solo muestra enlaces activos (Enlace::scopePublicado).
 */
class ListaEnlacesPublica extends Component
{
    #[Url(as: 'q')]
    public string $busqueda = '';

    public function limpiarFiltros(): void
    {
        $this->reset('busqueda');
    }

    public function render()
    {
        $categorias = CategoriaEnlace::query()
            ->orderBy('orden')
            ->with(['enlaces' => function ($consulta) {
                $consulta->publicado()
                    ->when($this->busqueda !== '', fn ($q) => $q->buscar($this->busqueda));
            }])
            ->get()
            ->filter(fn (CategoriaEnlace $categoria) => $categoria->enlaces->isNotEmpty());

        return view('livewire.publico.lista-enlaces-publica', [
            'categorias' => $categorias,
        ]);
    }
}
