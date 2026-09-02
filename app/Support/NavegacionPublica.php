<?php

namespace App\Support;

/**
 * Fuente única de la navegación pública del micrositio (header, menú móvil y footer)
 * para que header y footer nunca queden desincronizados entre sí.
 */
class NavegacionPublica
{
    /**
     * @return array<string, string> Etiqueta visible => nombre de ruta
     */
    public static function enlaces(): array
    {
        return [
            'Quiénes somos' => 'quienes-somos',
            'Normatividad' => 'normatividad',
            'Direcciones' => 'direcciones',
            'Noticias' => 'noticias',
            'Documentos' => 'documentos',
            'Galería' => 'galeria',
            'Enlaces de interés' => 'enlaces',
            'Contacto' => 'contacto',
        ];
    }
}
