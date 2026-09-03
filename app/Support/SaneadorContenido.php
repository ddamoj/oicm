<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\File;

/**
 * Saneado en servidor del HTML enriquecido de noticias (Fase 5, RF-NOT-001).
 * El editor Quill del navegador nunca es de fiar: todo contenido pasa por
 * aquí antes de guardarse, con una lista blanca fija de etiquetas y
 * atributos para evitar XSS almacenado.
 */
class SaneadorContenido
{
    /**
     * Etiquetas y atributos permitidos, alineados con la barra de
     * herramientas del editor (resources/js/editor.js): si el editor no
     * ofrece una opción, tampoco se permite aquí.
     */
    private const HTML_PERMITIDO = 'p,br,strong,em,u,ul,ol,li,a[href|title],h2,h3,blockquote';

    public static function sanear(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', self::HTML_PERMITIDO);
        $config->set('HTML.TargetBlank', true);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $config->set('Attr.AllowedRel', ['noopener', 'noreferrer']);
        $config->set('HTML.TargetNoopener', true);
        $config->set('HTML.TargetNoreferrer', true);
        $rutaCache = storage_path('framework/cache/htmlpurifier');
        File::ensureDirectoryExists($rutaCache);
        $config->set('Cache.SerializerPath', $rutaCache);

        return (new HTMLPurifier($config))->purify($html);
    }
}
