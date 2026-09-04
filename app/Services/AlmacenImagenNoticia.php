<?php

namespace App\Services;

use App\Models\Noticia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Endurecimiento de la carga de la imagen de portada de noticias (Fase 5,
 * RF-NOT-001): recodifica siempre el archivo con GD (nunca se guarda el
 * binario subido tal cual, lo que elimina cualquier payload incrustado) y
 * genera una miniatura recortada para el listado público.
 */
class AlmacenImagenNoticia
{
    private const ANCHO_MAXIMO_PORTADA = 1600;

    private const ANCHO_MINIATURA = 600;

    private const ALTO_MINIATURA = 400;

    public function __construct(private readonly string $disco = 'public') {}

    /**
     * Recodifica y guarda la portada y su miniatura. Devuelve las rutas
     * listas para asignarse a los campos del modelo Noticia.
     *
     * @return array{imagen_portada: string, imagen_miniatura: string}
     */
    public function guardar(UploadedFile $archivo): array
    {
        $recurso = $this->cargarRecurso($archivo);
        $carpeta = 'noticias/'.now()->format('Y');
        $nombreBase = (string) Str::ulid();

        try {
            $portada = $this->redimensionar($recurso, self::ANCHO_MAXIMO_PORTADA, null);
            $rutaPortada = $this->guardarWebp($portada, $carpeta, $nombreBase.'.webp');
            imagedestroy($portada);

            $miniatura = $this->recortar($recurso, self::ANCHO_MINIATURA, self::ALTO_MINIATURA);
            $rutaMiniatura = $this->guardarWebp($miniatura, $carpeta, $nombreBase.'-miniatura.webp');
            imagedestroy($miniatura);
        } finally {
            imagedestroy($recurso);
        }

        return [
            'imagen_portada' => $rutaPortada,
            'imagen_miniatura' => $rutaMiniatura,
        ];
    }

    /**
     * Sustituye la imagen de una noticia existente, eliminando antes los
     * archivos anteriores para no dejar residuos en el disco público.
     *
     * @return array{imagen_portada: string, imagen_miniatura: string}
     */
    public function reemplazar(Noticia $noticia, UploadedFile $archivoNuevo): array
    {
        $this->eliminarArchivos($noticia);

        return $this->guardar($archivoNuevo);
    }

    public function eliminarArchivos(Noticia $noticia): void
    {
        if ($noticia->imagen_portada) {
            Storage::disk($this->disco)->delete($noticia->imagen_portada);
        }

        if ($noticia->imagen_miniatura) {
            Storage::disk($this->disco)->delete($noticia->imagen_miniatura);
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

    /**
     * Redimensiona conservando proporción. Si $altoMaximo es null, se calcula
     * a partir del ancho para no deformar la imagen.
     */
    private function redimensionar(\GdImage $original, int $anchoMaximo, ?int $altoMaximo): \GdImage
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

    /**
     * Recorte centrado a las proporciones exactas solicitadas (miniatura).
     */
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
        $rutaTemporal = tempnam(sys_get_temp_dir(), 'noticia_');
        imagewebp($imagen, $rutaTemporal, 85);

        $ruta = $carpeta.'/'.$nombreArchivo;
        Storage::disk($this->disco)->put($ruta, file_get_contents($rutaTemporal));
        unlink($rutaTemporal);

        return $ruta;
    }
}
