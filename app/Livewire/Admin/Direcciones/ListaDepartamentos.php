<?php

namespace App\Livewire\Admin\Direcciones;

use App\Models\BitacoraAuditoria;
use App\Models\Departamento;
use App\Models\Direccion;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Departamentos de una Dirección concreta: el segundo nivel del organigrama
 * público de "Quiénes somos".
 *
 * Los 11 departamentos sembrados en la Fase 7 llevan nombres provisionales
 * (el organigrama del documento de carga es una imagen, sin texto
 * extraíble). Esta pantalla existe para que el OICM pueda corregirlos sin
 * intervención de desarrollo en cuanto confirme los nombres oficiales.
 */
class ListaDepartamentos extends Component
{
    public Direccion $direccion;

    public function mount(Direccion $direccion): void
    {
        $this->direccion = $direccion;
    }

    public function nuevo(): void
    {
        $this->dispatch('departamento:nuevo', direccionId: $this->direccion->id);
    }

    public function editar(int $id): void
    {
        $this->dispatch('departamento:editar', id: $id);
    }

    /**
     * Baja lógica reversible: retira el departamento del organigrama público
     * sin perder el registro.
     */
    public function alternarActivo(int $id): void
    {
        try {
            $departamento = $this->departamentoDeLaDireccion($id);

            $this->authorize('update', $departamento);

            $departamento->update(['activo' => ! $departamento->activo]);

            BitacoraAuditoria::registrar($departamento->activo ? 'departamento_activado' : 'departamento_desactivado', 'Departamento', $departamento->id, [
                'nombre' => $departamento->nombre,
            ]);

            $this->dispatch('mostrar-exito', mensaje: $departamento->activo
                ? 'Departamento visible en el organigrama público.'
                : 'Departamento retirado del organigrama público.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al cambiar la visibilidad del departamento.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al actualizar el departamento.');
        }
    }

    public function eliminar(int $id): void
    {
        try {
            $departamento = $this->departamentoDeLaDireccion($id);

            $this->authorize('delete', $departamento);

            BitacoraAuditoria::registrar('departamento_eliminado', 'Departamento', $departamento->id, [
                'nombre' => $departamento->nombre,
                'direccion' => $this->direccion->nombre,
            ]);

            $departamento->delete();

            $this->dispatch('mostrar-exito', mensaje: 'Departamento eliminado correctamente.');
        } catch (AuthorizationException $excepcion) {
            $this->dispatch('mostrar-error', mensaje: $excepcion->getMessage() ?: 'No tienes permiso para realizar esta acción.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al eliminar el departamento.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al eliminar el departamento.');
        }
    }

    /**
     * Resuelve el departamento acotándolo siempre a la Dirección de la ruta:
     * un id manipulado desde el cliente no puede alcanzar departamentos de
     * otra Dirección.
     */
    private function departamentoDeLaDireccion(int $id): Departamento
    {
        return Departamento::query()
            ->where('direccion_id', $this->direccion->id)
            ->findOrFail($id);
    }

    /**
     * Sin cuerpo: recibir el evento basta para que Livewire vuelva a
     * renderizar este componente con la lista actualizada tras guardar.
     */
    #[On('departamento-guardado')]
    public function alGuardar(): void {}

    /**
     * @return Collection<int, Departamento>
     */
    private function departamentos(): Collection
    {
        return $this->direccion->departamentos()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    public function render()
    {
        $this->authorize('viewAny', Departamento::class);

        return view('livewire.admin.direcciones.lista-departamentos', [
            'departamentos' => $this->departamentos(),
        ]);
    }
}
