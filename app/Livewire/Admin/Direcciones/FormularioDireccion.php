<?php

namespace App\Livewire\Admin\Direcciones;

use App\Models\BitacoraAuditoria;
use App\Models\Direccion;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Alta y edición de una Dirección del OICM. Se monta una sola vez dentro del
 * modal de la lista y cambia de modo por eventos Livewire, igual que el resto
 * de formularios del panel.
 */
class FormularioDireccion extends Component
{
    public ?int $direccionId = null;

    public string $nombre = '';

    public string $siglas = '';

    public string $descripcion = '';

    public string $orden = '0';

    public bool $activa = true;

    /**
     * Clave actual en modo edición. Se muestra como solo lectura: forma parte
     * de la ruta pública `/direcciones/{clave}`, así que cambiarla rompería
     * los enlaces ya publicados y compartidos.
     */
    public string $clave = '';

    protected function reglas(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:200'],
            'siglas' => ['nullable', 'string', 'max:20'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function mensajes(): array
    {
        return [
            'nombre.required' => 'El nombre de la Dirección es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'siglas.max' => 'Las siglas no pueden exceder 20 caracteres.',
            'orden.integer' => 'El orden debe ser un número entero.',
        ];
    }

    #[On('direccion:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-direccion');
    }

    #[On('direccion:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $direccion = Direccion::query()->findOrFail($id);

            $this->direccionId = $direccion->id;
            $this->clave = $direccion->clave;
            $this->nombre = $direccion->nombre;
            $this->siglas = (string) $direccion->siglas;
            $this->descripcion = (string) $direccion->descripcion;
            $this->orden = (string) $direccion->orden;
            $this->activa = $direccion->activa;

            $this->dispatch('abrir-modal', nombre: 'formulario-direccion');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar la Dirección a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar la Dirección seleccionada.');
        }
    }

    private function resetear(): void
    {
        $this->reset(['direccionId', 'clave', 'nombre', 'siglas', 'descripcion', 'orden']);
        $this->activa = true;
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-configuracion')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->direccionId === null;

            $datos = [
                'nombre' => $datosValidados['nombre'],
                'siglas' => $datosValidados['siglas'] ?: null,
                'descripcion' => $datosValidados['descripcion'] ?: null,
                'orden' => $datosValidados['orden'] !== null ? (int) $datosValidados['orden'] : 0,
                'activa' => $this->activa,
            ];

            if ($esAlta) {
                $direccion = Direccion::query()->create([
                    ...$datos,
                    'clave' => $this->generarClaveUnica($datosValidados['nombre']),
                ]);

                BitacoraAuditoria::registrar('direccion_creada', 'Direccion', $direccion->id, [
                    'nombre' => $direccion->nombre,
                    'clave' => $direccion->clave,
                ]);
            } else {
                $direccion = Direccion::query()->findOrFail($this->direccionId);

                // La `clave` nunca se recalcula al editar: es la URL pública
                // de la Dirección y ya puede estar difundida.
                $direccion->update($datos);

                BitacoraAuditoria::registrar('direccion_actualizada', 'Direccion', $direccion->id, [
                    'nombre' => $direccion->nombre,
                ]);
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-direccion');
            $this->dispatch('direccion-guardada');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Dirección creada correctamente.' : 'Dirección actualizada correctamente.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar la Dirección.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar la Dirección. Intenta de nuevo.');
        }
    }

    /**
     * Genera una clave única a partir del nombre, añadiendo un sufijo
     * numérico si ya existe otra igual (la columna tiene índice único).
     * Considera también las Direcciones dadas de baja lógica: su clave sigue
     * ocupando el índice.
     */
    private function generarClaveUnica(string $nombre): string
    {
        $base = Str::slug($nombre) ?: 'direccion';
        $clave = $base;
        $sufijo = 1;

        while (Direccion::query()->withTrashed()->where('clave', $clave)->exists()) {
            $sufijo++;
            $clave = $base.'-'.$sufijo;
        }

        return $clave;
    }

    public function render()
    {
        return view('livewire.admin.direcciones.formulario-direccion');
    }
}
