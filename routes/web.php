<?php

use App\Http\Controllers\ContactoController;
use App\Http\Controllers\DocumentoDescargaController;
use App\Http\Controllers\EstradoDescargaController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\InstitucionalController;
use App\Http\Controllers\NoticiaController;
use App\Models\Documento;
use App\Models\Enlace;
use App\Models\Galeria;
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
    // Portada (Fase 8): últimas noticias, documentos recientes y accesos
    // rápidos marcados por el Administrador de Contenido.
    return view('publico.inicio', [
        'ultimasNoticias' => Noticia::query()->publicado()->limit(3)->get(),
        'documentosRecientes' => Documento::query()->publicado()->with('categoria')->latest()->limit(4)->get(),
        'accesosRapidos' => Enlace::query()->publicado()->where('destacado_inicio', true)->get(),
    ]);
})->name('inicio');

// Buscador global del micrositio (Fase 8): consulta varios modelos a la vez,
// respetando siempre el scopePublicado de cada uno.
Route::get('/buscar', function () {
    return view('publico.buscar');
})->name('buscar')->middleware('throttle:60,1');

// Galería de fotos y videos por evento (Fase 8), sin autenticación.
Route::get('/galeria', [GaleriaController::class, 'index'])->name('galeria');
Route::get('/galeria/{galeria}', [GaleriaController::class, 'show'])->name('galeria.mostrar');

// Contacto: directorio, mapa y canal de quejas y denuncias (Fase 8), sin autenticación.
Route::get('/contacto', ContactoController::class)->name('contacto');

// Información institucional, normatividad y direcciones (Fase 7, RF-INS), sin autenticación.
Route::get('/quienes-somos', [InstitucionalController::class, 'quienesSomos'])->name('quienes-somos');
Route::get('/direcciones', [InstitucionalController::class, 'listaDirecciones'])->name('direcciones');
Route::get('/direcciones/{direccion:clave}', [InstitucionalController::class, 'direccion'])->name('direcciones.mostrar');

Route::get('/normatividad', function () {
    return view('publico.normatividad');
})->name('normatividad');

// Estrados digitales de la DRACS (Fase 7): consulta y descarga sin autenticación,
// con el mismo throttle de descarga que el repositorio de documentos (Fase 4).
Route::get('/estrados', function () {
    return view('publico.estrados');
})->name('estrados');

Route::get('/estrados/{estrado}/descargar', EstradoDescargaController::class)
    ->name('estrados.descargar')
    ->middleware('throttle:30,1');

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

        // Contenido institucional, normatividad y estrados digitales (Fase 7).
        Route::get('/paginas', function () {
            return view('admin.paginas');
        })->name('paginas');

        Route::get('/normatividad', function () {
            return view('admin.normatividad');
        })->name('normatividad');

        Route::get('/estrados', function () {
            return view('admin.estrados');
        })->name('estrados');

        // Galería y contacto (Fase 8).
        Route::get('/galerias', function () {
            return view('admin.galerias');
        })->name('galerias');

        Route::get('/galerias/{galeria}/medios', function (Galeria $galeria) {
            return view('admin.galerias-medios', ['galeria' => $galeria]);
        })->name('galerias.medios');

        Route::get('/contactos', function () {
            return view('admin.contactos');
        })->name('contactos');
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
