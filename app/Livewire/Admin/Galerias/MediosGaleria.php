<?php

namespace App\Livewire\Admin\Galerias;

use App\Models\BitacoraAuditoria;
use App\Models\Galeria;
use App\Models\GaleriaMedio;
use App\Services\AlmacenMedioGaleria;
use App\Support\ReglasImagen;
use App\Support\ReglasVideoGaleria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Gestor de medios de una galería (Fase 8): sube fotos y videos, registra
 * video por URL externa (YouTube/Vimeo) y permite reordenar/eliminar.
 * Página completa (no modal), porque agrupa varias subidas independientes.
 */
class MediosGaleria extends Component
{
    use WithFileUploads;

    public Galeria $galeria;

    public string $tipo = 'foto';

    public mixed $archivo = null;

    public string $urlExterna = '';

    public string $descripcionAlt = '';

    public function mount(Galeria $galeria): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $this->galeria = $galeria;
    }

    protected function reglas(): array
    {
        if ($this->tipo === 'foto') {
            return [
                'archivo' => ReglasImagen::reglas(requerido: true),
                'descripcionAlt' => ['required', 'string', 'max:250'],
            ];
        }

        if ($this->urlExterna !== '') {
            return [
                // Esquema restringido a http/https, mismo criterio que Enlace.
                'urlExterna' => ['required', 'string', 'max:500', 'url:http,https'],
                'descripcionAlt' => ['nullable', 'string', 'max:250'],
            ];
        }

        return [
            'archivo' => ReglasVideoGaleria::reglas(requerido: true),
            'descripcionAlt' => ['nullable', 'string', 'max:250'],
        ];
    }

    protected function mensajes(): array
    {
        return array_merge(
            ReglasImagen::mensajes('archivo'),
            ReglasVideoGaleria::mensajes('archivo'),
            [
                'descripcionAlt.required' => 'Describe la foto para lectores de pantalla (texto alternativo).',
                'urlExterna.required' => 'Ingresa la URL del video.',
                'urlExterna.url' => 'Ingresa una URL válida que empiece con http:// o https://.',
            ]
        );
    }

    public function agregar(): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $almacen = app(AlmacenMedioGaleria::class);
            $siguienteOrden = (int) $this->galeria->medios()->max('orden') + 1;

            if ($this->tipo === 'foto') {
                if (! ReglasImagen::mimeRealValido($this->archivo)) {
                    $this->addError('archivo', 'El contenido del archivo no coincide con un formato de imagen permitido.');

                    return;
                }

                $rutas = $almacen->guardarFoto($this->archivo, $this->galeria->id);

                GaleriaMedio::query()->create([
                    'galeria_id' => $this->galeria->id,
                    'tipo' => 'foto',
                    'ruta_archivo' => $rutas['ruta_archivo'],
                    'ruta_miniatura' => $rutas['ruta_miniatura'],
                    'descripcion_alt' => $datosValidados['descripcionAlt'],
                    'orden' => $siguienteOrden,
                ]);
            } elseif ($this->urlExterna !== '') {
                $medio = new GaleriaMedio(['url_externa' => $datosValidados['urlExterna']]);

                if ($medio->proveedorVideoExterno() === null) {
                    $this->addError('urlExterna', 'Solo se aceptan URL de YouTube o Vimeo.');

                    return;
                }

                GaleriaMedio::query()->create([
                    'galeria_id' => $this->galeria->id,
                    'tipo' => 'video',
                    'url_externa' => $datosValidados['urlExterna'],
                    'descripcion_alt' => $datosValidados['descripcionAlt'] ?: null,
                    'orden' => $siguienteOrden,
                ]);
            } else {
                if (! ReglasVideoGaleria::mimeRealValido($this->archivo)) {
                    $this->addError('archivo', 'El contenido del archivo no coincide con un formato de video permitido.');

                    return;
                }

                $rutas = $almacen->guardarVideo($this->archivo, $this->galeria->id);

                GaleriaMedio::query()->create([
                    'galeria_id' => $this->galeria->id,
                    'tipo' => 'video',
                    'ruta_archivo' => $rutas['ruta_archivo'],
                    'descripcion_alt' => $datosValidados['descripcionAlt'] ?: null,
                    'orden' => $siguienteOrden,
                ]);
            }

            BitacoraAuditoria::registrar('galeria_medio_agregado', 'Galeria', $this->galeria->id, [
                'tipo' => $this->tipo,
            ]);

            $this->reset(['archivo', 'urlExterna', 'descripcionAlt']);
            $this->resetErrorBag();
            $this->dispatch('mostrar-exito', mensaje: 'Medio agregado a la galería.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al agregar un medio a la galería.', ['galeria_id' => $this->galeria->id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al agregar el medio. Intenta de nuevo.');
        }
    }

    public function eliminar(int $medioId): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        try {
            $medio = $this->galeria->medios()->findOrFail($medioId);

            app(AlmacenMedioGaleria::class)->eliminarArchivos($medio);
            $medio->delete();

            BitacoraAuditoria::registrar('galeria_medio_eliminado', 'Galeria', $this->galeria->id, [
                'medio_id' => $medioId,
            ]);

            $this->dispatch('mostrar-exito', mensaje: 'Medio eliminado de la galería.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar un medio de la galería.', ['medio_id' => $medioId, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar el medio.');
        }
    }

    /**
     * Intercambia el orden con el medio inmediato anterior/siguiente
     * (subir/bajar), sin necesidad de arrastrar y soltar.
     */
    public function mover(int $medioId, string $direccion): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        try {
            $medios = $this->galeria->medios()->orderBy('orden')->get();
            $indice = $medios->search(fn ($medio) => $medio->id === $medioId);

            if ($indice === false) {
                return;
            }

            $indiceVecino = $direccion === 'arriba' ? $indice - 1 : $indice + 1;

            if (! $medios->has($indiceVecino)) {
                return;
            }

            DB::transaction(function () use ($medios, $indice, $indiceVecino) {
                $ordenActual = $medios[$indice]->orden;
                $medios[$indice]->update(['orden' => $medios[$indiceVecino]->orden]);
                $medios[$indiceVecino]->update(['orden' => $ordenActual]);
            });
        } catch (\Throwable $excepcion) {
            Log::error('Error al reordenar los medios de la galería.', ['medio_id' => $medioId, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al reordenar los medios.');
        }
    }

    public function render()
    {
        $medios = $this->galeria->medios()->orderBy('orden')->get();

        return view('livewire.admin.galerias.medios-galeria', ['medios' => $medios]);
    }
}
