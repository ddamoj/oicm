<?php

namespace App\Livewire\Admin\Enlaces;

use App\Models\BitacoraAuditoria;
use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Alta y edición de un enlace de interés (RF-ENL-001). Se monta una sola vez
 * dentro del modal de la lista y cambia de modo por eventos Livewire, igual
 * que el formulario de documentos.
 */
class FormularioEnlace extends Component
{
    public ?int $enlaceId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public string $categoriaEnlaceId = '';

    public string $url = '';

    public string $orden = '0';

    public bool $activo = true;

    protected function reglas(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:200'],
            'categoriaEnlaceId' => ['required', 'exists:categorias_enlace,id'],
            // Esquema restringido a http/https: evita enlaces `javascript:`/`data:`
            // que abrirían un vector de XSS al hacer clic en el directorio público.
            'url' => ['required', 'string', 'max:500', 'url:http,https'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function mensajes(): array
    {
        return [
            'nombre.required' => 'El nombre del enlace es obligatorio.',
            'categoriaEnlaceId.required' => 'Selecciona una categoría.',
            'categoriaEnlaceId.exists' => 'La categoría seleccionada no es válida.',
            'url.required' => 'La URL es obligatoria.',
            'url.url' => 'Ingresa una URL válida que empiece con http:// o https://.',
        ];
    }

    #[On('enlace:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-enlace');
    }

    #[On('enlace:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $enlace = Enlace::query()->findOrFail($id);

            $this->enlaceId = $enlace->id;
            $this->nombre = $enlace->nombre;
            $this->descripcion = (string) $enlace->descripcion;
            $this->categoriaEnlaceId = (string) $enlace->categoria_enlace_id;
            $this->url = $enlace->url;
            $this->orden = (string) $enlace->orden;
            $this->activo = $enlace->activo;

            $this->dispatch('abrir-modal', nombre: 'formulario-enlace');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar el enlace a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar el enlace seleccionado.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['enlaceId', 'nombre', 'descripcion', 'categoriaEnlaceId', 'url', 'orden']);
        $this->activo = true;
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->enlaceId === null;

            $datos = [
                'categoria_enlace_id' => (int) $datosValidados['categoriaEnlaceId'],
                'nombre' => $datosValidados['nombre'],
                'descripcion' => $datosValidados['descripcion'] ?: null,
                'url' => $datosValidados['url'],
                'orden' => $datosValidados['orden'] !== null ? (int) $datosValidados['orden'] : 0,
                'activo' => $this->activo,
            ];

            if ($esAlta) {
                $enlace = Enlace::query()->create($datos);

                BitacoraAuditoria::registrar('enlace_creado', 'Enlace', $enlace->id, [
                    'nombre' => $enlace->nombre,
                ]);
            } else {
                $enlace = Enlace::query()->findOrFail($this->enlaceId);
                $enlace->update($datos);

                BitacoraAuditoria::registrar('enlace_actualizado', 'Enlace', $enlace->id, [
                    'nombre' => $enlace->nombre,
                ]);
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-enlace');
            $this->dispatch('enlace-guardado');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Enlace creado correctamente.' : 'Enlace actualizado correctamente.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar el enlace.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar el enlace. Intenta de nuevo.');
        }
    }

    #[Computed]
    public function categorias(): Collection
    {
        return CategoriaEnlace::query()->orderBy('orden')->get();
    }

    public function render()
    {
        return view('livewire.admin.enlaces.formulario-enlace');
    }
}
