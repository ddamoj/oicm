<?php

use App\Http\Controllers\DocumentoDescargaController;
use App\Http\Controllers\NoticiaController;
use App\Models\Noticia;
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
    // Últimas 3 noticias publicadas (Fase 5, RF-NOT-002) para la portada.
    return view('publico.inicio', [
        'ultimasNoticias' => Noticia::query()->publicado()->limit(3)->get(),
    ]);
})->name('inicio');

foreach (
    [
        'quienes-somos' => 'Quiénes somos',
        'normatividad' => 'Normatividad',
        'direcciones' => 'Direcciones',
        'galeria' => 'Galería',
        'contacto' => 'Contacto',
    ] as $ruta => $titulo
) {
    Route::get("/{$ruta}", function () use ($titulo) {
        return view('publico.en-construccion', ['titulo' => $titulo]);
    })->name($ruta);
}

// Repositorio público de documentos (Fase 4, RF-DES-001/002): consulta y
// descarga sin autenticación. La descarga se limita con throttle para
// mitigar abuso automatizado del ancho de banda.
Route::get('/documentos', function () {
    return view('publico.documentos');
})->name('documentos');

Route::get('/documentos/{documento}/descargar', DocumentoDescargaController::class)
    ->name('documentos.descargar')
    ->middleware('throttle:30,1');

// Noticias, avisos y comunicados (Fase 5, RF-NOT-001/002/003), sin autenticación.
Route::get('/noticias', function () {
    return view('publico.noticias');
})->name('noticias');

Route::get('/noticias/{noticia:slug}', [NoticiaController::class, 'show'])->name('noticias.mostrar');

// Directorio de enlaces de interés (Fase 6, RF-ENL-001/002), sin autenticación.
Route::get('/enlaces', function () {
    return view('publico.enlaces');
})->name('enlaces');

/*
|--------------------------------------------------------------------------
| Rutas administrativas
|--------------------------------------------------------------------------
| Requieren sesión autenticada y cuenta activa (revocación inmediata de
| acceso, RF-USR-001). El CRUD real de cada módulo se construye en las
| fases 3 a 6; aquí se deja el layout de administración navegable.
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'cuenta.activa',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.en-construccion', ['titulo' => 'Panel principal']);
    })->name('panel');

    Route::get('/perfil', function () {
        return view('admin.perfil');
    })->name('perfil');

    // Documentos y noticias: administrador y administrador de contenido
    // (RF-CAR-001/002/003, RF-NOT-001/002/003).
    Route::middleware('rol:administrador,administrador_contenido')->group(function () {
        Route::get('/documentos', function () {
            return view('admin.documentos');
        })->name('documentos');

        Route::get('/noticias', function () {
            return view('admin.noticias');
        })->name('noticias');

        Route::get('/enlaces', function () {
            return view('admin.enlaces');
        })->name('enlaces');
    });

    // Usuarios y bitácora: exclusivos del rol "administrador" (RF-USR-001).
    Route::middleware('rol:administrador')->group(function () {
        Route::get('/usuarios', function () {
            return view('admin.usuarios');
        })->name('usuarios');

        Route::get('/bitacora', function () {
            return view('admin.bitacora');
        })->name('bitacora');
    });
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
