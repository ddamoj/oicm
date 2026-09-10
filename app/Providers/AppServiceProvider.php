<?php

namespace App\Providers;

use App\Models\User;
use App\Models\VisitaPagina;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->definirCompuertasDeAcceso();

        // Nonce de CSP por petición (Fase 9): @vite y @livewireScripts lo
        // adjuntan solos a las etiquetas <script> que generan.
        $this->app->make(Vite::class)->useCspNonce();

        $this->compartirContadorDeVisitas();
    }

    /**
     * Deja el contador de visitas a disposición del pie de página público.
     *
     * La cifra se cachea cinco minutos: se sirve en cada página del
     * micrositio y no necesita ser exacta al segundo. La caché se deja
     * expirar sola —no se invalida en cada visita nueva—, porque hacerlo
     * obligaría a recalcular en cada petición y anularía el propósito de
     * cachearla.
     */
    private function compartirContadorDeVisitas(): void
    {
        View::composer('components.layouts.publico', function ($vista): void {
            $contador = Cache::remember('visitas.contador-publico', now()->addMinutes(5), function (): array {
                try {
                    return [
                        'hoy' => VisitaPagina::query()->hoy()->count(),
                        'total' => VisitaPagina::query()->count(),
                    ];
                } catch (\Throwable) {
                    // El pie de página nunca debe tumbar el micrositio: ante
                    // un fallo de la consulta simplemente no se muestra nada.
                    return ['hoy' => 0, 'total' => 0];
                }
            });

            $vista->with('contadorVisitas', $contador);
        });
    }

    /**
     * Compuertas de autorización por módulo (RF-USR-001/002). Todas exigen una
     * cuenta activa: una sesión abierta de un usuario desactivado/eliminado no
     * debe poder ejecutar acciones administrativas aunque no haya expirado.
     */
    private function definirCompuertasDeAcceso(): void
    {
        Gate::define('gestionar-usuarios', fn (User $usuario): bool => $usuario->activo && $usuario->tieneRol('administrador'));

        Gate::define('gestionar-contenido', fn (User $usuario): bool => $usuario->activo
            && ($usuario->tieneRol('administrador') || $usuario->tieneRol('administrador_contenido')));

        Gate::define('gestionar-configuracion', fn (User $usuario): bool => $usuario->activo && $usuario->tieneRol('administrador'));
    }
}
