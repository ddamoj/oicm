<?php

namespace App\Livewire\Admin\Galerias;

use App\Models\BitacoraAuditoria;
use App\Models\Galeria;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Alta y edición de una galería (título, descripción, fecha de evento y
 * estatus de publicación). La carga de medios individuales se hace aparte,
 * en App\Livewire\Admin\Galerias\MediosGaleria, una vez creada la galería.
 */
class FormularioGaleria extends Component
{
    public ?int $galeriaId = null;

    public string $titulo = '';

    public string $descripcion = '';

    public string $fechaEvento = '';

    public bool $publicada = true;

    protected function reglas(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'fechaEvento' => ['nullable', 'date'],
        ];
    }

    protected function mensajes(): array
    {
        return [
            'titulo.required' => 'El título de la galería es obligatorio.',
            'fechaEvento.date' => 'Ingresa una fecha válida.',
        ];
    }

    #[On('galeria:nueva')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-galeria');
    }

    #[On('galeria:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $galeria = Galeria::query()->findOrFail($id);

            $this->galeriaId = $galeria->id;
            $this->titulo = $galeria->titulo;
            $this->descripcion = (string) $galeria->descripcion;
            $this->fechaEvento = $galeria->fecha_evento?->format('Y-m-d') ?? '';
            $this->publicada = $galeria->publicada;

            $this->dispatch('abrir-modal', nombre: 'formulario-galeria');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar la galería a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar la galería seleccionada.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['galeriaId', 'titulo', 'descripcion', 'fechaEvento']);
        $this->publicada = true;
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->galeriaId === null;

            $datos = [
                'titulo' => $datosValidados['titulo'],
                'descripcion' => $datosValidados['descripcion'] ?: null,
                'fecha_evento' => $datosValidados['fechaEvento'] ?: null,
                'publicada' => $this->publicada,
            ];

            if ($esAlta) {
                $galeria = Galeria::query()->create($datos);

                BitacoraAuditoria::registrar('galeria_creada', 'Galeria', $galeria->id, [
                    'titulo' => $galeria->titulo,
                ]);
            } else {
                $galeria = Galeria::query()->findOrFail($this->galeriaId);
                $galeria->update($datos);

                BitacoraAuditoria::registrar('galeria_actualizada', 'Galeria', $galeria->id, [
                    'titulo' => $galeria->titulo,
                ]);
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-galeria');
            $this->dispatch('galeria-guardada');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Galería creada correctamente. Ahora agrega sus fotos y videos.' : 'Galería actualizada correctamente.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar la galería.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar la galería. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.admin.galerias.formulario-galeria');
    }
}
