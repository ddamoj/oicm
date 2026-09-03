<?php

namespace App\Livewire\Publico;

use App\Models\CategoriaDocumento;
use App\Models\Direccion;
use App\Models\Documento;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Vista pública del repositorio de documentos, sin autenticación
 * (RF-DES-001/002): filtro por categoría, por dirección responsable (Fase 7,
 * enlace desde la ficha de cada Dirección) y buscador por nombre/descripción.
 * Solo muestra documentos con publicado = true (Documento::scopePublicado).
 */
class ListaDocumentosPublica extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $busqueda = '';

    #[Url(as: 'categoria')]
    public string $filtroCategoria = '';

    #[Url(as: 'direccion')]
    public string $filtroDireccion = '';

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroCategoria', 'filtroDireccion'], true)) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'filtroCategoria', 'filtroDireccion']);
        $this->resetPage();
    }

    public function render()
    {
        $documentos = Documento::query()
            ->publicado()
            ->with(['categoria', 'direccion'])
            ->when($this->busqueda !== '', fn ($consulta) => $consulta->buscar($this->busqueda))
            ->when($this->filtroCategoria !== '', fn ($consulta) => $consulta->porCategoria((int) $this->filtroCategoria))
            ->when($this->filtroDireccion !== '', fn ($consulta) => $consulta->porDireccion((int) $this->filtroDireccion))
            ->orderBy('nombre')
            ->paginate(12);

        return view('livewire.publico.lista-documentos-publica', [
            'documentos' => $documentos,
            'categorias' => CategoriaDocumento::query()->orderBy('orden')->get(),
            'direccionActiva' => $this->filtroDireccion !== '' ? Direccion::query()->find($this->filtroDireccion) : null,
        ]);
    }
}
