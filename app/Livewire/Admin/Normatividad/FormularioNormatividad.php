<?php

namespace App\Livewire\Admin\Normatividad;

use App\Models\BitacoraAuditoria;
use App\Models\Normatividad;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Alta y edición de un ordenamiento del marco normativo (Fase 7). Se monta
 * una sola vez dentro del modal de la lista y cambia de modo por eventos
 * Livewire, igual que el formulario de enlaces.
 */
class FormularioNormatividad extends Component
{
    public ?int $normatividadId = null;

    public string $ambito = 'federal';

    public string $titulo = '';

    public string $descripcion = '';

    public string $medioPublicacion = '';

    public string $fechaPublicacion = '';

    public string $fechaUltimaReforma = '';

    public string $documentoUrl = '';

    public string $orden = '0';

    public bool $vigente = true;

    protected function reglas(): array
    {
        return [
            'ambito' => ['required', 'in:federal,estatal,municipal'],
            'titulo' => ['required', 'string', 'max:300'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'medioPublicacion' => ['nullable', 'string', 'max:200'],
            'fechaPublicacion' => ['nullable', 'date'],
            // La última reforma no puede ser anterior a la publicación original.
            'fechaUltimaReforma' => ['nullable', 'date', 'after_or_equal:fechaPublicacion'],
            // Esquema restringido a http/https: evita enlaces `javascript:`/`data:`.
            'documentoUrl' => ['nullable', 'string', 'max:500', 'url:http,https'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function mensajes(): array
    {
        return [
            'ambito.required' => 'Selecciona el ámbito.',
            'titulo.required' => 'El título del ordenamiento es obligatorio.',
            'fechaUltimaReforma.after_or_equal' => 'La última reforma no puede ser anterior a la fecha de publicación.',
            'documentoUrl.url' => 'Ingresa una URL válida que empiece con http:// o https://.',
        ];
    }

    #[On('normatividad:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-normatividad');
    }

    #[On('normatividad:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $normatividad = Normatividad::query()->findOrFail($id);

            $this->normatividadId = $normatividad->id;
            $this->ambito = $normatividad->ambito;
            $this->titulo = $normatividad->titulo;
            $this->descripcion = (string) $normatividad->descripcion;
            $this->medioPublicacion = (string) $normatividad->medio_publicacion;
            $this->fechaPublicacion = $normatividad->fecha_publicacion?->format('Y-m-d') ?? '';
            $this->fechaUltimaReforma = $normatividad->fecha_ultima_reforma?->format('Y-m-d') ?? '';
            $this->documentoUrl = (string) $normatividad->documento_url;
            $this->orden = (string) $normatividad->orden;
            $this->vigente = $normatividad->vigente;

            $this->dispatch('abrir-modal', nombre: 'formulario-normatividad');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar el ordenamiento a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar el ordenamiento seleccionado.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['normatividadId', 'titulo', 'descripcion', 'medioPublicacion', 'fechaPublicacion', 'fechaUltimaReforma', 'documentoUrl', 'orden']);
        $this->ambito = 'federal';
        $this->vigente = true;
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->normatividadId === null;

            $datos = [
                'ambito' => $datosValidados['ambito'],
                'titulo' => $datosValidados['titulo'],
                'descripcion' => $datosValidados['descripcion'] ?: null,
                'medio_publicacion' => $datosValidados['medioPublicacion'] ?: null,
                'fecha_publicacion' => $datosValidados['fechaPublicacion'] ?: null,
                'fecha_ultima_reforma' => $datosValidados['fechaUltimaReforma'] ?: null,
                'documento_url' => $datosValidados['documentoUrl'] ?: null,
                'orden' => $datosValidados['orden'] !== null ? (int) $datosValidados['orden'] : 0,
                'vigente' => $this->vigente,
            ];

            if ($esAlta) {
                $normatividad = Normatividad::query()->create($datos);

                BitacoraAuditoria::registrar('normatividad_creada', 'Normatividad', $normatividad->id, [
                    'titulo' => $normatividad->titulo,
                ]);
            } else {
                $normatividad = Normatividad::query()->findOrFail($this->normatividadId);
                $normatividad->update($datos);

                BitacoraAuditoria::registrar('normatividad_actualizada', 'Normatividad', $normatividad->id, [
                    'titulo' => $normatividad->titulo,
                ]);
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-normatividad');
            $this->dispatch('normatividad-guardada');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Ordenamiento creado correctamente.' : 'Ordenamiento actualizado correctamente.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar el ordenamiento.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar el ordenamiento. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.admin.normatividad.formulario-normatividad');
    }
}
