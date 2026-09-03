<?php

namespace App\Livewire\Admin\Documentos;

use App\Models\BitacoraAuditoria;
use App\Models\CategoriaDocumento;
use App\Models\Direccion;
use App\Models\Documento;
use App\Services\AlmacenDocumentos;
use App\Support\ReglasDocumento;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Alta, edición de metadatos y reemplazo de archivo de un documento
 * (RF-CAR-001/002/003). Se monta una sola vez dentro del modal de la lista y
 * cambia de modo por eventos Livewire, igual que el formulario de usuarios.
 */
class FormularioDocumento extends Component
{
    use WithFileUploads;

    public ?int $documentoId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public string $categoriaDocumentoId = '';

    public string $direccionId = '';

    public bool $publicado = true;

    /** Archivo nuevo: obligatorio al crear, opcional al editar (reemplazo). */
    public mixed $archivo = null;

    /** True cuando el modal se abrió específicamente para reemplazar el archivo. */
    public bool $modoReemplazo = false;

    protected function reglas(): array
    {
        $esAlta = $this->documentoId === null;

        return [
            'nombre' => ['required', 'string', 'max:250'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'categoriaDocumentoId' => ['required', 'exists:categorias_documento,id'],
            'direccionId' => ['nullable', 'exists:direcciones,id'],
            'archivo' => ReglasDocumento::reglas(requerido: $esAlta || $this->modoReemplazo),
        ];
    }

    protected function mensajes(): array
    {
        return array_merge([
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'categoriaDocumentoId.required' => 'Selecciona una categoría.',
            'categoriaDocumentoId.exists' => 'La categoría seleccionada no es válida.',
            'direccionId.exists' => 'La dirección seleccionada no es válida.',
        ], ReglasDocumento::mensajes('archivo'));
    }

    #[On('documento:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-documento');
    }

    #[On('documento:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->cargarParaEdicion($id, modoReemplazo: false);
    }

    #[On('documento:reemplazar')]
    public function prepararReemplazo(int $id): void
    {
        $this->cargarParaEdicion($id, modoReemplazo: true);
    }

    private function cargarParaEdicion(int $id, bool $modoReemplazo): void
    {
        $this->resetear();

        try {
            $documento = Documento::query()->findOrFail($id);

            $this->documentoId = $documento->id;
            $this->nombre = $documento->nombre;
            $this->descripcion = (string) $documento->descripcion;
            $this->categoriaDocumentoId = (string) $documento->categoria_documento_id;
            $this->direccionId = $documento->direccion_id ? (string) $documento->direccion_id : '';
            $this->publicado = $documento->publicado;
            $this->modoReemplazo = $modoReemplazo;

            $this->dispatch('abrir-modal', nombre: 'formulario-documento');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar el documento a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar el documento seleccionado.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['documentoId', 'nombre', 'descripcion', 'categoriaDocumentoId', 'direccionId', 'archivo', 'modoReemplazo']);
        $this->publicado = true;
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->documentoId === null;
            $categoria = CategoriaDocumento::query()->findOrFail($datosValidados['categoriaDocumentoId']);

            // Segunda verificación, independiente de la regla `mimes:`: el MIME real
            // del archivo debe estar en la lista blanca (RF-CAR-002, rechazo sin
            // almacenar ante un archivo cuyo contenido no coincide con su extensión).
            if ($this->archivo && ! ReglasDocumento::mimeRealValido($this->archivo)) {
                $this->addError('archivo', 'El contenido del archivo no coincide con un formato permitido.');

                return;
            }

            if ($esAlta) {
                $datosArchivo = app(AlmacenDocumentos::class)->guardar($this->archivo, $categoria);

                $documento = Documento::query()->create([
                    'categoria_documento_id' => $categoria->id,
                    'direccion_id' => $this->direccionId !== '' ? (int) $this->direccionId : null,
                    'nombre' => $datosValidados['nombre'],
                    'descripcion' => $datosValidados['descripcion'] ?: null,
                    'publicado' => $this->publicado,
                    'subido_por' => auth()->id(),
                    ...$datosArchivo,
                ]);

                BitacoraAuditoria::registrar('documento_creado', 'Documento', $documento->id, [
                    'nombre' => $documento->nombre,
                    'categoria' => $categoria->clave,
                ]);
            } else {
                $documento = Documento::query()->findOrFail($this->documentoId);

                $documento->fill([
                    'categoria_documento_id' => $categoria->id,
                    'direccion_id' => $this->direccionId !== '' ? (int) $this->direccionId : null,
                    'nombre' => $datosValidados['nombre'],
                    'descripcion' => $datosValidados['descripcion'] ?: null,
                    'publicado' => $this->publicado,
                ]);
                $documento->save();

                BitacoraAuditoria::registrar('documento_actualizado', 'Documento', $documento->id, [
                    'nombre' => $documento->nombre,
                ]);

                if ($this->archivo) {
                    app(AlmacenDocumentos::class)->reemplazar($documento, $this->archivo, auth()->user());

                    BitacoraAuditoria::registrar('documento_archivo_reemplazado', 'Documento', $documento->id, [
                        'nombre' => $documento->nombre,
                    ]);
                }
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-documento');
            $this->dispatch('documento-guardado');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Documento cargado correctamente.' : 'Documento actualizado correctamente.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar el documento.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar el documento. Intenta de nuevo.');
        }
    }

    #[Computed]
    public function categorias(): Collection
    {
        return CategoriaDocumento::query()->orderBy('orden')->get();
    }

    #[Computed]
    public function direcciones(): Collection
    {
        return Direccion::activas()->get();
    }

    public function render()
    {
        return view('livewire.admin.documentos.formulario-documento');
    }
}
