<?php

namespace App\Services;

use App\Models\CategoriaDocumento;
use App\Models\Documento;
use App\Models\DocumentoVersion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Endurecimiento de la carga y el reemplazo de archivos del repositorio
 * público de documentos (Fase 4, RF-CAR-001/002/003). Centraliza aquí el
 * saneamiento de nombres y el almacenamiento fuera de la raíz web para que
 * Livewire y las pruebas usen siempre el mismo camino.
 */
class AlmacenDocumentos
{
    public function __construct(private readonly string $disco = 'documentos') {}

    /**
     * Guarda el archivo de un documento nuevo en el disco privado y devuelve
     * los datos listos para crear el registro. El nombre físico es generado
     * por el servidor (ULID); el nombre original del usuario solo se guarda
     * como dato, nunca se usa para construir la ruta.
     *
     * @return array{ruta_archivo: string, nombre_original: string, extension: string, mime_type: string, tamano_bytes: int}
     */
    public function guardar(UploadedFile $archivo, CategoriaDocumento $categoria): array
    {
        $extension = strtolower($archivo->getClientOriginalExtension());
        $rutaCarpeta = trim($categoria->clave, '/').'/'.now()->format('Y');
        $nombreFisico = (string) Str::ulid().'.'.$extension;

        $rutaGuardada = $archivo->storeAs($rutaCarpeta, $nombreFisico, $this->disco);

        if ($rutaGuardada === false) {
            throw new \RuntimeException('No fue posible guardar el archivo en el disco de documentos.');
        }

        return [
            'ruta_archivo' => $rutaGuardada,
            'nombre_original' => $this->sanearNombreOriginal($archivo->getClientOriginalName()),
            'extension' => $extension,
            'mime_type' => (string) $archivo->getMimeType(),
            'tamano_bytes' => $archivo->getSize(),
        ];
    }

    /**
     * Reemplaza el archivo vigente de un documento: conserva la versión
     * anterior en `documento_versiones` (RF-CAR-002) y solo después de dejarla
     * a salvo apunta el documento al archivo nuevo. Todo dentro de una
     * transacción para no perder la versión previa si algo falla a medio camino.
     */
    public function reemplazar(Documento $documento, UploadedFile $archivoNuevo, User $usuario): void
    {
        $datosNuevos = $this->guardar($archivoNuevo, $documento->categoria);

        DB::transaction(function () use ($documento, $datosNuevos, $usuario) {
            $siguienteVersion = (int) $documento->versiones()->max('numero_version') + 1;

            DocumentoVersion::query()->create([
                'documento_id' => $documento->id,
                'numero_version' => $siguienteVersion,
                'ruta_archivo' => $documento->ruta_archivo,
                'nombre_original' => $documento->nombre_original,
                'extension' => $documento->extension,
                'mime_type' => $documento->mime_type,
                'tamano_bytes' => $documento->tamano_bytes,
                'reemplazado_por' => $usuario->id,
            ]);

            $documento->update($datosNuevos);
        });
    }

    /**
     * Elimina físicamente el archivo vigente y el de todas sus versiones
     * anteriores. Se usa al dar de baja definitivamente un documento.
     */
    public function eliminarArchivos(Documento $documento): void
    {
        Storage::disk($this->disco)->delete($documento->ruta_archivo);

        foreach ($documento->versiones as $version) {
            Storage::disk($this->disco)->delete($version->ruta_archivo);
        }
    }

    /**
     * Quita cualquier segmento de ruta y caracteres fuera de una lista blanca
     * del nombre original, para que nunca se use tal cual en una respuesta de
     * descarga (evita inyección de cabeceras o path traversal por esa vía).
     */
    private function sanearNombreOriginal(string $nombre): string
    {
        $nombre = basename($nombre);
        $nombre = preg_replace('/[^A-Za-z0-9 ._-]/', '', $nombre) ?? $nombre;

        return mb_substr(trim($nombre), 0, 250);
    }
}
