<?php

namespace App\Livewire\Admin\Estrados;

use App\Models\BitacoraAuditoria;
use App\Models\Estrado;
use App\Services\AlmacenEstrados;
use App\Support\ReglasDocumento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Publicación de un estrado digital de la DRACS (Fase 7, riesgo 9 del plan de
 * trabajo): el número de folio es un consecutivo asignado por el sistema al
 * publicar, y el archivo queda con una huella SHA-256 como constancia de
 * integridad. Solo se permite publicar (no editar el archivo): un estrado
 * notificado no debe cambiar de contenido una vez consultado por el público.
 */
class FormularioEstrado extends Component
{
    use WithFileUploads;

    public string $expediente = '';

    public string $asunto = '';

    public mixed $archivo = null;

    public bool $datosTestados = false;

    protected function reglas(): array
    {
        return [
            'expediente' => ['nullable', 'string', 'max:100'],
            'asunto' => ['required', 'string', 'max:300'],
            // Solo PDF: los estrados son notificaciones formales, no formatos editables.
            'archivo' => ['required', 'file', 'mimes:pdf', 'max:'.ReglasDocumento::TAMANO_MAXIMO_KB],
            // Confirmación obligatoria de que el documento ya pasó por el
            // procedimiento de testado de datos personales antes de publicarse.
            'datosTestados' => ['accepted'],
        ];
    }

    protected function mensajes(): array
    {
        return [
            'asunto.required' => 'El asunto de la notificación es obligatorio.',
            'archivo.required' => 'Selecciona el archivo PDF del estrado.',
            'archivo.mimes' => 'Solo se acepta un archivo en formato PDF.',
            'archivo.max' => 'El archivo supera el tamaño máximo permitido de 25 MB.',
            'datosTestados.accepted' => 'Confirma que el documento ya fue testado (versión pública) antes de publicarlo.',
        ];
    }

    #[On('estrado:nuevo')]
    public function prepararAlta(): void
    {
        $this->resetear();
        $this->dispatch('abrir-modal', nombre: 'formulario-estrado');
    }

    private function resetear(): void
    {
        $this->reset(['expediente', 'asunto', 'archivo']);
        $this->datosTestados = false;
        $this->resetErrorBag();
    }

    public function guardar(AlmacenEstrados $almacen): void
    {
        if (! Gate::allows('gestionar-contenido')) {
            abort(403);
        }

        $datosValidados = $this->validate($this->reglas(), $this->mensajes());

        try {
            // Segunda verificación, independiente de la regla `mimes:`: el MIME
            // real del archivo debe ser PDF (rechazo sin almacenar ante un
            // archivo cuyo contenido no coincide con lo que su extensión declara).
            if ($this->archivo->getMimeType() !== 'application/pdf') {
                $this->addError('archivo', 'El contenido del archivo no coincide con un PDF válido.');

                return;
            }

            $estrado = DB::transaction(function () use ($datosValidados, $almacen) {
                $datosArchivo = $almacen->guardar($this->archivo);

                return Estrado::query()->create([
                    'numero' => $almacen->siguienteNumero(),
                    'expediente' => $datosValidados['expediente'] ?: null,
                    'asunto' => $datosValidados['asunto'],
                    'fecha_publicacion' => now(),
                    'datos_testados' => true,
                    'publicado_por' => auth()->id(),
                    'activo' => true,
                    ...$datosArchivo,
                ]);
            });

            BitacoraAuditoria::registrar('estrado_publicado', 'Estrado', $estrado->id, [
                'numero' => $estrado->numero,
                'asunto' => $estrado->asunto,
            ]);

            $this->dispatch('cerrar-modal', nombre: 'formulario-estrado');
            $this->dispatch('estrado-guardado');
            $this->dispatch('mostrar-exito', mensaje: "Estrado número {$estrado->numero} publicado correctamente.");
            $this->resetear();
        } catch (\Throwable $excepcion) {
            Log::error('Error al publicar el estrado digital.', ['error' => $excepcion->getMessage()]);
            $this->dispatch('mostrar-error', mensaje: 'Ocurrió un problema al publicar el estrado. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.admin.estrados.formulario-estrado');
    }
}
