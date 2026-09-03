<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Política de video subido a la galería (Fase 8): lista blanca de
 * extensiones y MIME reales, y tamaño máximo. Mismo papel que ReglasImagen
 * y ReglasDocumento: punto único de definición para que Livewire y
 * App\Services\AlmacenMedioGaleria nunca queden desalineados entre sí.
 */
class ReglasVideoGaleria
{
    public const TAMANO_MAXIMO_KB = 80 * 1024;

    /** @var array<int, string> */
    public const EXTENSIONES = ['mp4', 'webm'];

    /** @var array<int, string> */
    public const MIME_PERMITIDOS = ['video/mp4', 'video/webm'];

    /**
     * @return array<int, string>
     */
    public static function reglas(bool $requerido = true): array
    {
        return [
            $requerido ? 'required' : 'nullable',
            'file',
            'mimes:'.implode(',', self::EXTENSIONES),
            'max:'.self::TAMANO_MAXIMO_KB,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function mensajes(string $campo = 'video'): array
    {
        return [
            "{$campo}.required" => 'Selecciona un video.',
            "{$campo}.mimes" => 'Formato no permitido. Solo se aceptan MP4 y WEBM.',
            "{$campo}.max" => 'El video supera el tamaño máximo permitido de 80 MB.',
        ];
    }

    /**
     * Segunda verificación, independiente de la regla `mimes:`: confirma que
     * el MIME real detectado por el servidor esté en la lista blanca.
     */
    public static function mimeRealValido(UploadedFile $archivo): bool
    {
        $mimeReal = $archivo->getMimeType();

        return $mimeReal !== null && in_array($mimeReal, self::MIME_PERMITIDOS, strict: true);
    }
}
