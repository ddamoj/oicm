<?php

namespace App\Livewire\Admin\Direcciones;

use App\Models\BitacoraAuditoria;
use App\Models\Departamento;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Alta y edición de un departamento dentro de una Dirección. Se monta una
 * sola vez dentro del modal de [ListaDepartamentos] y cambia de modo por
 * eventos Livewire.
 */
class FormularioDepartamento extends Component
{
    public ?int $departamentoId = null;

    /** Dirección a la que se adscribe el departamento (viene de la ruta). */
    public int $direccionId = 0;

    public string $nombre = '';

    public string $siglas = '';

    public string $descripcion = '';

    public string $orden = '0';

    public bool $activo = true;

    protected function reglas(): array
    {
        return [
            'direccionId' => ['required', 'exists:direcciones,id'],
            'nombre' => ['required', 'string', 'max:200'],
            'siglas' => ['nullable', 'string', 'max:20'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function mensajes(): array
    {
        return [
            'direccionId.required' => 'El departamento debe pertenecer a una Dirección.',
            'direccionId.exists' => 'La Dirección seleccionada no es válida.',
            'nombre.required' => 'El nombre del departamento es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'siglas.max' => 'Las siglas no pueden exceder 20 caracteres.',
            'orden.integer' => 'El orden debe ser un número entero.',
        ];
    }

    #[On('departamento:nuevo')]
    public function prepararAlta(int $direccionId): void
    {
        $this->resetear();
        $this->direccionId = $direccionId;
        $this->dispatch('abrir-modal', nombre: 'formulario-departamento');
    }

    #[On('departamento:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $departamento = Departamento::query()->findOrFail($id);

            $this->departamentoId = $departamento->id;
            $this->direccionId = $departamento->direccion_id;
            $this->nombre = $departamento->nombre;
            $this->siglas = (string) $departamento->siglas;
            $this->descripcion = (string) $departamento->descripcion;
            $this->orden = (string) $departamento->orden;
            $this->activo = $departamento->activo;

            $this->dispatch('abrir-modal', nombre: 'formulario-departamento');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar el departamento a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar el departamento seleccionado.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['departamentoId', 'nombre', 'siglas', 'descripcion', 'orden']);
        $this->activo = true;
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-configuracion')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->departamentoId === null;

            $datos = [
                'direccion_id' => (int) $datosValidados['direccionId'],
                'nombre' => $datosValidados['nombre'],
                'siglas' => $datosValidados['siglas'] ?: null,
                'descripcion' => $datosValidados['descripcion'] ?: null,
                'orden' => $datosValidados['orden'] !== null ? (int) $datosValidados['orden'] : 0,
                'activo' => $this->activo,
            ];

            if ($esAlta) {
                $departamento = Departamento::query()->create($datos);

                BitacoraAuditoria::registrar('departamento_creado', 'Departamento', $departamento->id, [
                    'nombre' => $departamento->nombre,
                ]);
            } else {
                $departamento = Departamento::query()->findOrFail($this->departamentoId);
                $departamento->update($datos);

                BitacoraAuditoria::registrar('departamento_actualizado', 'Departamento', $departamento->id, [
                    'nombre' => $departamento->nombre,
                ]);
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-departamento');
            $this->dispatch('departamento-guardado');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Departamento creado correctamente.' : 'Departamento actualizado correctamente.');
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar el departamento.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar el departamento. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.admin.direcciones.formulario-departamento');
    }
}
