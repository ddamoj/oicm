<?php

namespace App\Livewire\Publico;

use App\Models\CategoriaDocumento;
use App\Models\Documento;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Vista pública del repositorio de documentos, sin autenticación
 * (RF-DES-001/002): filtro por categoría y buscador por nombre/descripción.
 * Solo muestra documentos con publicado = true (Documento::scopePublicado).
 */
class ListaDocumentosPublica extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $busqueda = '';

    #[Url(as: 'categoria')]
    public string $filtroCategoria = '';

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroCategoria'], true)) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'filtroCategoria']);
        $this->resetPage();
    }

    public function render()
    {
        $documentos = Documento::query()
            ->publicado()
            ->with('categoria')
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->buscar($this->busqueda))
            ->when($this->filtroCategoria !== '', fn ($consulta) => $consulta->porCategoria((int) $this->filtroCategoria))
            ->orderBy('nombre')
            ->paginate(12);

        return view('livewire.publico.lista-documentos-publica', [
            'documentos' => $documentos,
            'categorias' => CategoriaDocumento::query()->orderBy('orden')->get(),
        ]);
    }
}
