<?php

namespace App\Services;

use App\Models\GaleriaMedio;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Endurecimiento de la carga de medios de galería (Fase 8): las fotos se
 * recodifican siempre con GD (mismo criterio que AlmacenImagenNoticia, elimina
 * cualquier payload incrustado) y los videos se validan por MIME real y se
 * guardan con nombre generado por el servidor, nunca con el nombre original.
 */
class AlmacenMedioGaleria
{
    private const ANCHO_MAXIMO_FOTO = 1920;

    private const ANCHO_MINIATURA = 600;

    private const ALTO_MINIATURA = 400;

    /** @var array<string, string> */
    private const EXTENSIONES_VIDEO = [
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
    ];

    public function __construct(private readonly string $disco = 'public') {}

    /**
     * Recodifica y guarda una foto junto con su miniatura para el grid público.
     *
     * @return array{ruta_archivo: string, ruta_miniatura: string}
     */
    public function guardarFoto(UploadedFile $archivo, int $galeriaId): array
    {
        $recurso = $this->cargarRecurso($archivo);
        $carpeta = "galerias/{$galeriaId}";
        $nombreBase = (string) Str::ulid();

        try {
            $foto = $this->redimensionar($recurso, self::ANCHO_MAXIMO_FOTO);
            $rutaArchivo = $this->guardarWebp($foto, $carpeta, $nombreBase.'.webp');
            imagedestroy($foto);

            $miniatura = $this->recortar($recurso, self::ANCHO_MINIATURA, self::ALTO_MINIATURA);
            $rutaMiniatura = $this->guardarWebp($miniatura, $carpeta, $nombreBase.'-miniatura.webp');
            imagedestroy($miniatura);
        } finally {
            imagedestroy($recurso);
        }

        return ['ruta_archivo' => $rutaArchivo, 'ruta_miniatura' => $rutaMiniatura];
    }

    /**
     * Guarda un video subido (MP4/WebM) validando el MIME real del archivo,
     * no la extensión declarada por el navegador.
     *
     * @return array{ruta_archivo: string}
     */
    public function guardarVideo(UploadedFile $archivo, int $galeriaId): array
    {
        $mime = (string) $archivo->getMimeType();
        $extension = self::EXTENSIONES_VIDEO[$mime] ?? null;

        if ($extension === null) {
            throw new \RuntimeException('Formato de video no soportado. Usa MP4 o WebM.');
        }

        $carpeta = "galerias/{$galeriaId}";
        $nombreFisico = (string) Str::ulid().'.'.$extension;

        $rutaGuardada = $archivo->storeAs($carpeta, $nombreFisico, $this->disco);

        if ($rutaGuardada === false) {
            throw new \RuntimeException('No fue posible guardar el video en el disco público.');
        }

        return ['ruta_archivo' => $rutaGuardada];
    }

    /**
     * Elimina del disco los archivos físicos asociados a un medio (foto,
     * miniatura o video subido); no aplica a medios con URL externa.
     */
    public function eliminarArchivos(GaleriaMedio $medio): void
    {
        if ($medio->ruta_archivo) {
            Storage::disk($this->disco)->delete($medio->ruta_archivo);
        }

        if ($medio->ruta_miniatura) {
            Storage::disk($this->disco)->delete($medio->ruta_miniatura);
        }
    }

    private function cargarRecurso(UploadedFile $archivo): \GdImage
    {
        $recurso = match ($archivo->getMimeType()) {
            'image/jpeg' => imagecreatefromjpeg($archivo->getRealPath()),
            'image/png' => imagecreatefrompng($archivo->getRealPath()),
            'image/webp' => imagecreatefromwebp($archivo->getRealPath()),
            default => throw new \RuntimeException('Formato de imagen no soportado.'),
        };

        if ($recurso === false) {
            throw new \RuntimeException('No fue posible procesar la imagen seleccionada.');
        }

        return $recurso;
    }

    private function redimensionar(\GdImage $original, int $anchoMaximo): \GdImage
    {
        $anchoOriginal = imagesx($original);
        $altoOriginal = imagesy($original);

        $escala = min(1, $anchoMaximo / $anchoOriginal);
        $anchoDestino = max(1, (int) round($anchoOriginal * $escala));
        $altoDestino = max(1, (int) round($altoOriginal * $escala));

        $destino = imagecreatetruecolor($anchoDestino, $altoDestino);
        imagecopyresampled($destino, $original, 0, 0, 0, 0, $anchoDestino, $altoDestino, $anchoOriginal, $altoOriginal);

        return $destino;
    }

    private function recortar(\GdImage $original, int $anchoDestino, int $altoDestino): \GdImage
    {
        $anchoOriginal = imagesx($original);
        $altoOriginal = imagesy($original);

        $escala = max($anchoDestino / $anchoOriginal, $altoDestino / $altoOriginal);
        $anchoEscalado = (int) ceil($anchoOriginal * $escala);
        $altoEscalado = (int) ceil($altoOriginal * $escala);

        $escalado = imagecreatetruecolor($anchoEscalado, $altoEscalado);
        imagecopyresampled($escalado, $original, 0, 0, 0, 0, $anchoEscalado, $altoEscalado, $anchoOriginal, $altoOriginal);

        $recorteX = (int) (($anchoEscalado - $anchoDestino) / 2);
        $recorteY = (int) (($altoEscalado - $altoDestino) / 2);

        $destino = imagecreatetruecolor($anchoDestino, $altoDestino);
        imagecopy($destino, $escalado, 0, 0, $recorteX, $recorteY, $anchoDestino, $altoDestino);
        imagedestroy($escalado);

        return $destino;
    }

    private function guardarWebp(\GdImage $imagen, string $carpeta, string $nombreArchivo): string
    {
        $rutaTemporal = tempnam(sys_get_temp_dir(), 'galeria_');
        imagewebp($imagen, $rutaTemporal, 85);

        $ruta = $carpeta.'/'.$nombreArchivo;
        Storage::disk($this->disco)->put($ruta, file_get_contents($rutaTemporal));
        unlink($rutaTemporal);

        return $ruta;
    }
}
