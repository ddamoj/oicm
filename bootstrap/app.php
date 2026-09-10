<?php

use App\Http\Middleware\CabecerasSeguridad;
use App\Http\Middleware\RegistrarVisita;
use App\Http\Middleware\VerificarCuentaActiva;
use App\Http\Middleware\VerificarRol;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol' => VerificarRol::class,
            'cuenta.activa' => VerificarCuentaActiva::class,
        ]);

        // Cabeceras de seguridad (Fase 9) en toda respuesta web: CSP, HSTS,
        // X-Frame-Options, etc. Se aplica globalmente para no depender de que
        // cada ruta nueva la declare por separado.
        //
        // `RegistrarVisita` sigue el mismo criterio: cuenta las visitas de las
        // páginas públicas y descarta por su cuenta el panel, el flujo de
        // autenticación y las peticiones internas de Livewire.
        $middleware->web(append: [CabecerasSeguridad::class, RegistrarVisita::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
