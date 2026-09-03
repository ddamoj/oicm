<?php

namespace App\Livewire\Admin\Contactos;

use App\Models\BitacoraAuditoria;
use App\Models\Contacto;
use App\Models\Direccion;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Alta y edición de una entrada del directorio de contacto (Fase 8): área,
 * dirección asociada, domicilio, teléfono, correo, horario, coordenadas del
 * mapa y canal de quejas y denuncias (reservado, en la práctica, a la fila
 * de la DQDISP).
 */
class FormularioContacto extends Component
{
    public ?int $contactoId = null;

    public string $nombreArea = '';

    public string $direccionId = '';

    public string $domicilio = '';

    public string $telefono = '';

    public string $correo = '';

    public string $horario = '';

    public string $latitud = '';

    public string $longitud = '';

    public string $canalQuejasDenuncias = '';

    public string $orden = '0';

    protected function reglas(): array
    {
        return [
            'nombreArea' => ['required', 'string', 'max:200'],
            'direccionId' => ['nullable', 'exists:direcciones,id'],
            'domicilio' => ['nullable', 'string', 'max:300'],
            'telefono' => ['nullable', 'regex:/^[0-9+\-\s()]{7,40}$/'],
            'correo' => ['nullable', 'email', 'max:150'],
            'horario' => ['nullable', 'string', 'max:150'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'canalQuejasDenuncias' => ['nullable', 'string', 'max:500'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function mensajes(): array
    {
        return [
            'nombreArea.required' => 'El nombre del área es obligatorio.',
            'telefono.regex' => 'Ingresa un teléfono válido.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
            'latitud.between' => 'La latitud debe estar entre -90 y 90.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
        ];
    }

    #[Computed]
    public function direcciones()
    {
        return Direccion::query()->orderBy('nombre')->get();
    }

    #[On('contacto:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-contacto');
    }

    #[On('contacto:editar')]
    public function prepararEdicion(int $id): void
    {
        $this->resetear();

        try {
            $contacto = Contacto::query()->findOrFail($id);

            $this->contactoId = $contacto->id;
            $this->nombreArea = $contacto->nombre_area;
            $this->direccionId = (string) $contacto->direccion_id;
            $this->domicilio = (string) $contacto->domicilio;
            $this->telefono = (string) $contacto->telefono;
            $this->correo = (string) $contacto->correo;
            $this->horario = (string) $contacto->horario;
            $this->latitud = $contacto->latitud !== null ? (string) $contacto->latitud : '';
            $this->longitud = $contacto->longitud !== null ? (string) $contacto->longitud : '';
            $this->canalQuejasDenuncias = (string) $contacto->canal_quejas_denuncias;
            $this->orden = (string) $contacto->orden;

            $this->dispatch('abrir-modal', nombre: 'formulario-contacto');
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar el contacto a editar.', ['id' => $id, 'error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'No fue posible cargar el contacto seleccionado.');
        }
    }

    private function resetear(): void
    {
        $this->reset([
            'contactoId', 'nombreArea', 'direccionId', 'domicilio', 'telefono',
            'correo', 'horario', 'latitud', 'longitud', 'canalQuejasDenuncias', 'orden',
        ]);
        $this->resetErrorBag();
    }

    public function guardar(): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            $esAlta = $this->contactoId === null;

            $datos = [
                'nombre_area' => $datosValidados['nombreArea'],
                'direccion_id' => $datosValidados['direccionId'] !== null && $datosValidados['direccionId'] !== '' ? (int) $datosValidados['direccionId'] : null,
                'domicilio' => $datosValidados['domicilio'] ?: null,
                'telefono' => $datosValidados['telefono'] ?: null,
                'correo' => $datosValidados['correo'] ?: null,
                'horario' => $datosValidados['horario'] ?: null,
                'latitud' => $datosValidados['latitud'] !== null && $datosValidados['latitud'] !== '' ? (float) $datosValidados['latitud'] : null,
                'longitud' => $datosValidados['longitud'] !== null && $datosValidados['longitud'] !== '' ? (float) $datosValidados['longitud'] : null,
                'canal_quejas_denuncias' => $datosValidados['canalQuejasDenuncias'] ?: null,
                'orden' => $datosValidados['orden'] !== null ? (int) $datosValidados['orden'] : 0,
            ];

            if ($esAlta) {
                $contacto = Contacto::query()->create($datos);

                BitacoraAuditoria::registrar('contacto_creado', 'Contacto', $contacto->id, [
                    'nombre_area' => $contacto->nombre_area,
                ]);
            } else {
                $contacto = Contacto::query()->findOrFail($this->contactoId);
                $contacto->update($datos);

                BitacoraAuditoria::registrar('contacto_actualizado', 'Contacto', $contacto->id, [
                    'nombre_area' => $contacto->nombre_area,
                ]);
            }

            $this->dispatch('cerrar-modal', nombre: 'formulario-contacto');
            $this->dispatch('contacto-guardado');
            $this->dispatch('mostrar-exito', mensaje: $esAlta ? 'Contacto creado correctamente.' : 'Contacto actualizado correctamente.');
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al guardar el contacto.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al guardar el contacto. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.admin.contactos.formulario-contacto');
    }
}
