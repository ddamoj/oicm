<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Política de la imagen de portada de noticias (Fase 5, RF-NOT-001): lista
 * blanca de extensiones y MIME reales, tamaño y dimensiones mínimas. Mismo
 * papel que ReglasDocumento para el repositorio de documentos: punto único
 * de definición para que Livewire y el servicio de almacenamiento nunca
 * queden desalineados entre sí.
 */
class ReglasImagen
{
    public const TAMANO_MAXIMO_KB = 4 * 1024;

    public const ANCHO_MINIMO = 400;

    public const ALTO_MINIMO = 300;

    /**
     * @var array<int, string>
     */
    public const EXTENSIONES = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * @var array<int, string>
     */
    public const MIME_PERMITIDOS = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * @return array<int, string>
     */
    public static function reglas(bool $requerido = true): array
    {
        return [
            $requerido ? 'required' : 'nullable',
            'image',
            'mimes:'.implode(',', self::EXTENSIONES),
            'max:'.self::TAMANO_MAXIMO_KB,
            'dimensions:min_width='.self::ANCHO_MINIMO.',min_height='.self::ALTO_MINIMO,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function mensajes(string $campo = 'imagen'): array
    {
        return [
            "{$campo}.required" => 'Selecciona una imagen de portada.',
            "{$campo}.image" => 'El archivo seleccionado no es una imagen válida.',
            "{$campo}.mimes" => 'Formato no permitido. Solo se aceptan JPG, PNG y WEBP.',
            "{$campo}.max" => 'La imagen supera el tamaño máximo permitido de 4 MB.',
            "{$campo}.dimensions" => 'La imagen debe medir al menos '.self::ANCHO_MINIMO.'x'.self::ALTO_MINIMO.' píxeles.',
        ];
    }

    /**
     * Segunda verificación, independiente de la regla `mimes:`: confirma que
     * el MIME real detectado por el servidor esté en la lista blanca (protege
     * contra un archivo malicioso renombrado con una extensión de imagen).
     */
    public static function mimeRealValido(UploadedFile $archivo): bool
    {
        $mimeReal = $archivo->getMimeType();

        return $mimeReal !== null && in_array($mimeReal, self::MIME_PERMITIDOS, strict: true);
    }
}
