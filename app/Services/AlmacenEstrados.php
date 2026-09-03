<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Carga de archivos de los estrados digitales de la DRACS (Fase 7): reutiliza
 * el disco privado "documentos" (Fase 4) bajo su propia carpeta, y añade la
 * huella SHA-256 del archivo como constancia probatoria de publicación.
 */
class AlmacenEstrados
{
    public function __construct(private readonly string $disco = 'documentos') {}

    /**
     * Guarda el archivo del estrado y devuelve los datos listos para crear el
     * registro, incluyendo el hash SHA-256 del contenido publicado.
     *
     * @return array{archivo_ruta: string, archivo_nombre: string, archivo_extension: string, archivo_mime: string, archivo_tamano: int, archivo_hash: string}
     */
    public function guardar(UploadedFile $archivo): array
    {
        $extension = strtolower($archivo->getClientOriginalExtension());
        $rutaCarpeta = 'estrados/'.now()->format('Y');
        $nombreFisico = (string) Str::ulid().'.'.$extension;

        $rutaGuardada = $archivo->storeAs($rutaCarpeta, $nombreFisico, $this->disco);

        if ($rutaGuardada === false) {
            throw new \RuntimeException('No fue posible guardar el archivo del estrado en el disco de documentos.');
        }

        return [
            'archivo_ruta' => $rutaGuardada,
            'archivo_nombre' => $this->sanearNombreOriginal($archivo->getClientOriginalName()),
            'archivo_extension' => $extension,
            'archivo_mime' => (string) $archivo->getMimeType(),
            'archivo_tamano' => $archivo->getSize(),
            'archivo_hash' => hash_file('sha256', $archivo->getRealPath()),
        ];
    }

    /**
     * Calcula el siguiente número consecutivo dentro de una transacción, para
     * evitar que dos publicaciones concurrentes obtengan el mismo folio.
     */
    public function siguienteNumero(): int
    {
        return (int) DB::table('estrados')->lockForUpdate()->max('numero') + 1;
    }

    /**
     * Sin categoría propia: se guarda el mismo nombre saneado que usa
     * `AlmacenDocumentos` para no exponer el nombre original tal cual.
     */
    private function sanearNombreOriginal(string $nombre): string
    {
        $nombre = basename($nombre);
        $nombre = preg_replace('/[^A-Za-z0-9 ._-]/', '', $nombre) ?? $nombre;

        return mb_substr(trim($nombre), 0, 250);
    }
}
