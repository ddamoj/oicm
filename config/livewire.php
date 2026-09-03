<?php

use App\Support\ReglasDocumento;

return [

    'class_namespace' => 'App\\Livewire',

    'view_path' => resource_path('views/livewire'),

    'layout' => 'components.layouts.app',

    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    |
    | Livewire valida los archivos temporales con "max:12288" (12 MB) por
    | defecto, antes incluso de que el componente los reciba. El repositorio
    | de documentos del OICM (RF-CAR-001) admite hasta 25 MB, así que el
    | límite se sube aquí para que coincida con ReglasDocumento::TAMANO_MAXIMO_KB
    | — la validación fina de extensión/MIME se hace luego en el componente.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,
        'rules' => ['required', 'file', 'max:'.ReglasDocumento::TAMANO_MAXIMO_KB],
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5,
        'cleanup' => true,
    ],

    'render_on_redirect' => false,

    'legacy_model_binding' => false,

    'inject_assets' => true,

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    'inject_morph_markers' => true,

    'smart_wire_keys' => false,

    'pagination_theme' => 'tailwind',

    'release_token' => 'a',
];
