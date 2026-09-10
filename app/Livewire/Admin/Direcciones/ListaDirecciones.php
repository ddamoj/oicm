<?php

namespace App\Livewire\Admin\Direcciones;

use App\Models\BitacoraAuditoria;
use App\Models\Direccion;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Catálogo de Direcciones del OICM: la Oficina del Contralor y las cuatro
 * Direcciones de área que alimentan el organigrama público de "Quiénes
 * somos" y la página `/direcciones/{clave}`.
 *
 * Reservado al rol "administrador" desde la ruta (middleware `rol`) y
 * reforzado aquí con `DireccionPolicy`.
 */
class ListaDirecciones extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public string $filtroEstado = '';

    protected $queryString = ['busqueda', 'filtroEstado'];

    public function updating($propiedad): void
    {
        if (in_array($propiedad, ['busqueda', 'filtroEstado'], true)) {
            $this->resetPage();
        }
    }

    public function nuevo(): void
    {
        $this->dispatch('direccion:nuevo');
    }

    public function editar(int $id): void
    {
        $this->dispatch('direccion:editar', id: $id);
    }

    /**
     * Baja lógica reversible: retira la Dirección del organigrama público y
     * de la lista de Direcciones sin perder el registro ni sus relaciones.
     */
    public function alternarActiva(int $id): void
    {
        try {
            $direccion = Direccion::query()->findOrFail($id);

            $this->authorize('update', $direccion);

            $direccion->update(['activa' => ! $direccion->activa]);

            BitacoraAuditoria::registrar($direccion->activa ? 'direccion_activada' : 'direccion_desactivada', 'Direccion', $direccion->id, [
                'nombre' => $direccion->nombre,
            ]);

            $this->dispatch('mostrar-exito', mensaje: $direccion->activa
                ? 'Dirección visible en el organigrama público.'
                : 'Dirección retirada del organigrama público.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar la visibilidad de la Dirección.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar la Dirección.');
        }
    }

    /**
     * Baja definitiva (borrado lógico). Se rechaza mientras la Dirección
     * clasifique documentos, páginas institucionales o contactos: esos
     * registros quedarían apuntando a una Dirección inexistente y sus páginas
     * públicas dejarían de resolverse. Para ocultarla sin romper nada está
     * `alternarActiva`.
     */
    public function eliminar(int $id): void
    {
        try {
            $direccion = Direccion::query()->findOrFail($id);

            $this->authorize('delete', $direccion);

            $dependencias = $this->dependenciasDe($direccion);

            if ($dependencias !== []) {
                $this->dispatch('mostrar-error', mensaje: 'No se puede eliminar: la Dirección todavía tiene '.implode(', ', $dependencias).'. Retírala del organigrama si solo quieres ocultarla.');

                return;
            }

            BitacoraAuditoria::registrar('direccion_eliminada', 'Direccion', $direccion->id, [
                'nombre' => $direccion->nombre,
                'clave' => $direccion->clave,
            ]);

            // Los departamentos no tienen vida propia fuera de su Dirección:
            // se dan de baja en la misma transacción para no dejar huérfanos
            // (la cascada de la llave foránea no se dispara con SoftDeletes).
            DB::transaction(function () use ($direccion): void {
                $direccion->departamentos()->delete();
                $direccion->delete();
            });

            $this->dispatch('mostrar-exito', mensaje: 'Dirección eliminada correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar la Dirección.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar la Dirección.');
        }
    }

    /**
     * Contenido que todavía depende de la Dirección, descrito en lenguaje
     * llano para el mensaje de rechazo.
     *
     * @return list<string>
     */
    private function dependenciasDe(Direccion $direccion): array
    {
        $conteos = [
            'documento' => $direccion->documentos()->count(),
            'página institucional' => $direccion->paginas()->count(),
            'contacto' => $direccion->contactos()->count(),
        ];

        $dependencias = [];

        foreach ($conteos as $singular => $total) {
            if ($total > 0) {
                $dependencias[] = $total.' '.($total === 1 ? $singular : $this->pluralizar($singular));
            }
        }

        return $dependencias;
    }

    /**
     * Plural en español de las tres etiquetas usadas arriba; `Str::plural()`
     * aplica reglas del inglés y produciría "páginas institucionals".
     */
    private function pluralizar(string $singular): string
    {
        return match ($singular) {
            'documento' => 'documentos',
            'página institucional' => 'páginas institucionales',
            'contacto' => 'contactos',
            default => $singular,
        };
    }

    /**
     * Sin cuerpo: recibir el evento basta para que Livewire vuelva a
     * renderizar este componente con la lista actualizada tras guardar.
     */
    #[On('direccion-guardada')]
    public function alGuardar(): void {}

    public function render()
    {
        $this->authorize('viewAny', Direccion::class);

        $direcciones = Direccion::query()
            ->withCount(['departamentos', 'documentos', 'paginas', 'contactos'])
            ->when($this->busqueda !== '', function ($consulta): void {
                // Los comodines van como binding, nunca concatenados a la
                // consulta: sin superficie de inyección SQL.
                $termino = '%'.$this->busqueda.'%';

                $consulta->where(function ($interna) use ($termino): void {
                    $interna->where('nombre', 'like', $termino)
                        ->orWhere('siglas', 'like', $termino)
                        ->orWhere('descripcion', 'like', $termino);
                });
            })
            ->when($this->filtroEstado !== '', fn ($consulta) => $consulta->where('activa', $this->filtroEstado === 'activa'))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.admin.direcciones.lista-direcciones', [
            'direcciones' => $direcciones,
        ]);
    }
}
