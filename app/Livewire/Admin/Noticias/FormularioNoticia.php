<?php

namespace App\Livewire\Admin\Noticias;

use App\Models\BitacoraAuditoria;
use App\Models\Noticia;
use App\Services\AlmacenImagenNoticia;
use App\Support\ReglasImagen;
use App\Support\SaneadorContenido;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Alta y edición de noticias, avisos y comunicados (RF-NOT-001/002). Se monta
 * una sola vez dentro del modal de la lista y cambia de modo por eventos
 * Livewire, igual que el formulario de documentos.
 */
class FormularioNoticia extends Component
{
    use WithFileUploads;

    public ?int $noticiaId = null;

    public string $titulo = '';

    public string $resumen = '';

    public string $contenido = '';

    public string $estatus = 'borrador';

    /** Fecha/hora de publicación en formato datetime-local del navegador. */
    public string $publicadoEn = '';

    public mixed $imagen = null;

    public string $imagenAlt = '';

    /** Miniatura de la imagen ya guardada (edición), para mostrarla sin volver a subirla. */
    public ?string $imagenActualUrl = null;

    protected function reglas(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:250'],
            'resumen' => ['nullable', 'string', 'max:300'],
            'contenido' => ['required', 'string'],
            'estatus' => ['required', 'in:borrador,publicada'],
            'publicadoEn' => [$this->estatus === 'publicada' ? 'required' : 'nullable', 'date'],
            'imagen' => ReglasImagen::reglas(requerido: false),
            'imagenAlt' => [($this->imagen || $this->imagenActualUrl) ? 'required' : 'nullable', 'string', 'max:250'],
        ];
    }

    protected function mensajes(): array
    {
        return array_merge([
            'titulo.required' => 'El título es obligatorio.',
            'contenido.required' => 'El contenido de la noticia es obligatorio.',
            'publicadoEn.required' => 'Indica la fecha y hora de publicación.',
            'imagenAlt.required' => 'Describe la imagen para lectores de pantalla (texto alternativo).',
        ], ReglasImagen::mensajes('imagen'));
    }

    #[On('noticia:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-noticia');
    }

    #[On('noticia:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $noticia = Noticia::query()->findOrFail($id);

            $this->noticiaId = $noticia->id;
            $this->titulo = $noticia->titulo;
            $this->resumen = (string) $noticia->resumen;
            $this->contenido = $noticia->contenido;
            $this->estatus = $noticia->estatus;
            $this->publicadoEn = $noticia->publicado_en?->format('Y-m-d\TH:i') ?? '';
            $this->imagenAlt = (string) $noticia->imagen_alt;
            $this->imagenActualUrl = $noticia->urlMiniatura();

            $this->dispatch('abrir-modal', nombre: 'formulario-noticia');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar la noticia a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar la noticia seleccionada.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['noticiaId', 'titulo', 'resumen', 'contenido', 'imagen', 'imagenAlt', 'imagenActualUrl', 'publicadoEn']);
        $this->estatus = 'borrador';
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->noticiaId === null;

            // Segunda verificación, independiente de `mimes:`: el MIME real del
            // archivo debe estar en la lista blanca (rechazo sin almacenar ante
            // contenido que no coincide con lo que la extensión declara).
            if ($this->imagen && ! ReglasImagen::mimeRealValido($this->imagen)) {
                $this->addError('imagen', 'El contenido del archivo no coincide con un formato de imagen permitido.');

                return;
            }

            $contenidoSaneado = SaneadorContenido::sanear($datosValidados['contenido']);
            $publicadoEn = $datosValidados['estatus'] === 'publicada' && $datosValidados['publicadoEn']
                ? Carbon::parse($datosValidados['publicadoEn'])
                : null;

            $datos = [
                'titulo' => $datosValidados['titulo'],
                'resumen' => $datosValidados['resumen'] ?: null,
                'contenido' => $contenidoSaneado,
                'estatus' => $datosValidados['estatus'],
                'publicado_en' => $publicadoEn,
                'imagen_alt' => $datosValidados['imagenAlt'] ?: null,
            ];

            if ($esAlta) {
                $datos['slug'] = $this->generarSlugUnico($datosValidados['titulo']);
                $datos['autor_id'] = auth()->id();

                if ($this->imagen) {
                    $datos = array_merge($datos, app(AlmacenImagenNoticia::class)->guardar($this->imagen));
                }

                $noticia = Noticia::query()->create($datos);

                BitacoraAuditoria::registrar('noticia_creada', 'Noticia', $noticia->id, ['titulo' => $noticia->titulo]);
            } else {
                $noticia = Noticia::query()->findOrFail($this->noticiaId);

                if ($this->imagen) {
                    $datos = array_merge($datos, app(AlmacenImagenNoticia::class)->reemplazar($noticia, $this->imagen));
                }

                $noticia->update($datos);

                BitacoraAuditoria::registrar('noticia_actualizada', 'Noticia', $noticia->id, ['titulo' => $noticia->titulo]);
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-noticia');
            $this->dispatch('noticia-guardada');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Noticia creada correctamente.' : 'Noticia actualizada correctamente.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar la noticia.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar la noticia. Intenta de nuevo.');
        }
    }

    /**
     * Genera un slug único a partir del título, añadiendo un sufijo numérico
     * si ya existe uno igual (dos noticias con el mismo título, por ejemplo).
     */
    private function generarSlugUnico(string $titulo): string
    {
        $base = Str::slug($titulo);
        $slug = $base;
        $sufijo = 1;

        while (Noticia::query()->where('slug', $slug)->exists()) {
            $sufijo++;
            $slug = $base.'-'.$sufijo;
        }

        return $slug;
    }

    public function render()
    {
        return view('livewire.admin.noticias.formulario-noticia');
    }
}
