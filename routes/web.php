<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
| Sin autenticación (rol Visitante, ERS §2). El contenido real de cada
| sección se desarrolla en las fases 4 a 8; aquí solo se deja el layout y
| la navegación con una vista "en construcción" para poder verificarlos.
*/
Route::get('/', function () {
    return view('publico.inicio');
})->name('inicio');

foreach (
    [
        'quienes-somos' => 'Quiénes somos',
        'normatividad' => 'Normatividad',
        'direcciones' => 'Direcciones',
        'noticias' => 'Noticias',
        'documentos' => 'Documentos',
        'galeria' => 'Galería',
        'enlaces' => 'Enlaces de interés',
        'contacto' => 'Contacto',
    ] as $ruta => $titulo
) {
    Route::get("/{$ruta}", function () use ($titulo) {
        return view('publico.en-construccion', ['titulo' => $titulo]);
    })->name($ruta);
}

/*
|--------------------------------------------------------------------------
| Rutas administrativas
|--------------------------------------------------------------------------
| Requieren sesión autenticada. El CRUD real de cada módulo se construye en
| las fases 3 a 6; aquí se deja el layout de administración navegable.
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.en-construccion', ['titulo' => 'Panel principal']);
    })->name('panel');

    foreach (
        [
            'documentos' => 'Documentos',
            'noticias' => 'Noticias',
            'enlaces' => 'Enlaces',
            'usuarios' => 'Usuarios',
        ] as $ruta => $titulo
    ) {
        Route::get("/{$ruta}", function () use ($titulo) {
            return view('admin.en-construccion', ['titulo' => $titulo]);
        })->name($ruta);
    }
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.panel');
    })->name('dashboard');
});
