<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Política de archivos del repositorio público de documentos (Fase 4,
 * RF-CAR-001/002): lista blanca de extensiones y MIME reales, y tamaño
 * máximo. Punto único de definición para que Livewire y el servicio de
 * almacenamiento nunca queden desalineados entre sí.
 */
class ReglasDocumento
{
    /**
     * Tamaño máximo permitido, en kilobytes (parámetro nativo de la regla
     * `max:` de Laravel para archivos). 25 MB = 25 * 1024 KB.
     */
    public const TAMANO_MAXIMO_KB = 25 * 1024;

    /**
     * Extensiones aceptadas, en minúsculas.
     *
     * @var array<int, string>
     */
    public const EXTENSIONES = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];

    /**
     * MIME reales aceptados por extensión — se usa para rechazar un archivo
     * cuyo contenido no corresponda a lo que su extensión declara (por
     * ejemplo, un ejecutable renombrado a ".pdf").
     *
     * @var array<int, string>
     */
    public const MIME_PERMITIDOS = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
        'text/plain', // algunos navegadores reportan los .csv como text/plain
        'application/csv',
        'application/vnd.ms-office', // .doc/.xls detectados de forma genérica en algunos entornos
    ];

    /**
     * Reglas de validación Livewire/Laravel para el campo de archivo.
     *
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
    public static function mensajes(string $campo = 'archivo'): array
    {
        return [
            "{$campo}.required" => 'Selecciona un archivo para cargar.',
            "{$campo}.file" => 'El archivo seleccionado no es válido.',
            "{$campo}.mimes" => 'Formato no permitido. Solo se aceptan PDF, DOC, DOCX, XLS, XLSX y CSV.',
            "{$campo}.max" => 'El archivo supera el tamaño máximo permitido de 25 MB.',
        ];
    }

    /**
     * Segunda verificación, independiente de la regla `mimes:`: confirma que
     * el MIME real detectado por el servidor (no la extensión ni el MIME que
     * declara el navegador) esté en la lista blanca. Protege contra un
     * archivo malicioso renombrado con una extensión permitida.
     */
    public static function mimeRealValido(UploadedFile $archivo): bool
    {
        $mimeReal = $archivo->getMimeType();

        return $mimeReal !== null && in_array($mimeReal, self::MIME_PERMITIDOS, strict: true);
    }
}
