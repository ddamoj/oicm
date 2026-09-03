<?php

namespace App\Livewire\Admin\Paginas;

use App\Models\BitacoraAuditoria;
use App\Models\Direccion;
use App\Models\PaginaInstitucional;
use App\Services\VersionadorPaginas;
use App\Support\SaneadorContenido;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Alta y edición de una página institucional (Fase 7): "Quiénes somos" y las
 * páginas propias de cada Dirección. Cada guardado archiva el contenido
 * vigente en `pagina_institucional_versiones` antes de sobrescribirlo
 * (editor de bloques versionados) vía `VersionadorPaginas`.
 */
class FormularioPaginaInstitucional extends Component
{
    public ?int $paginaId = null;

    public string $titulo = '';

    public string $contenido = '';

    public string $direccionId = '';

    public string $estatus = 'borrador';

    protected function reglas(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:200'],
            'contenido' => ['required', 'string'],
            'direccionId' => ['nullable', 'exists:direcciones,id'],
            'estatus' => ['required', 'in:borrador,publicada'],
        ];
    }

    protected function mensajes(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'contenido.required' => 'El contenido de la página es obligatorio.',
            'direccionId.exists' => 'La dirección seleccionada no es válida.',
        ];
    }

    #[On('pagina:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-pagina');
    }

    #[On('pagina:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $pagina = PaginaInstitucional::query()->findOrFail($id);

            $this->paginaId = $pagina->id;
            $this->titulo = $pagina->titulo;
            $this->contenido = $pagina->contenido;
            $this->direccionId = (string) $pagina->direccion_id;
            $this->estatus = $pagina->estatus;

            $this->dispatch('abrir-modal', nombre: 'formulario-pagina');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar la página institucional a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar la página seleccionada.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['paginaId', 'titulo', 'contenido', 'direccionId']);
        $this->estatus = 'borrador';
        $this->resetErrorBag();
    }

    public function guardar(VersionadorPaginas $versionador): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->paginaId === null;
            $contenidoSaneado = SaneadorContenido::sanear($datosValidados['contenido']);
            $usuario = auth()->user();

            $datos = [
                'titulo' => $datosValidados['titulo'],
                'contenido' => $contenidoSaneado,
                'direccion_id' => $datosValidados['direccionId'] !== '' ? (int) $datosValidados['direccionId'] : null,
                'estatus' => $datosValidados['estatus'],
            ];

            if ($esAlta) {
                $pagina = PaginaInstitucional::query()->create([
                    ...$datos,
                    'slug' => $this->generarSlugUnico($datosValidados['titulo']),
                    'actualizado_por' => $usuario->id,
                ]);

                BitacoraAuditoria::registrar('pagina_institucional_creada', 'PaginaInstitucional', $pagina->id, [
                    'titulo' => $pagina->titulo,
                ]);
            } else {
                $pagina = PaginaInstitucional::query()->findOrFail($this->paginaId);

                // El slug de "quienes-somos" y de las páginas por Dirección es
                // parte de las rutas públicas: nunca se recalcula al editar.
                $versionador->guardar($pagina, $datos, $usuario);

                BitacoraAuditoria::registrar('pagina_institucional_actualizada', 'PaginaInstitucional', $pagina->id, [
                    'titulo' => $pagina->titulo,
                ]);
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-pagina');
            $this->dispatch('pagina-guardada');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Página creada correctamente.' : 'Página actualizada correctamente. Se guardó una versión del contenido anterior.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar la página institucional.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar la página. Intenta de nuevo.');
        }
    }

    /**
     * Genera un slug único a partir del título, añadiendo un sufijo numérico
     * si ya existe uno igual.
     */
    private function generarSlugUnico(string $titulo): string
    {
        $base = Str::slug($titulo);
        $slug = $base;
        $sufijo = 1;

        while (PaginaInstitucional::query()->where('slug', $slug)->exists()) {
            $sufijo++;
            $slug = $base.'-'.$sufijo;
        }

        return $slug;
    }

    /**
     * @return Collection<int, Direccion>
     */
    #[Computed]
    public function direcciones(): Collection
    {
        return Direccion::query()->orderBy('orden')->get();
    }

    public function render()
    {
        return view('livewire.admin.paginas.formulario-pagina-institucional');
    }
}
