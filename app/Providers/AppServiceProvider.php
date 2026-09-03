<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
