<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\BitacoraAuditoria;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        $this->autenticarConCuentaActiva();
        $this->registrarBitacoraDeAcceso();

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }

    /**
     * Autenticación propia (RF-USR-002): además de validar la contraseña,
     * rechaza a las cuentas desactivadas con un mensaje específico. Las
     * cuentas eliminadas (softDelete) ya quedan fuera de la consulta por el
     * scope global de `SoftDeletes` en el modelo `User`.
     */
    private function autenticarConCuentaActiva(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $usuario = User::query()->where('email', $request->input(Fortify::username()))->first();

            if (! $usuario || ! Hash::check((string) $request->password, $usuario->password)) {
                return null;
            }

            if (! $usuario->activo) {
                BitacoraAuditoria::registrar('login_rechazado_cuenta_desactivada', 'User', $usuario->id);

                throw ValidationException::withMessages([
                    Fortify::username() => 'Tu cuenta ha sido desactivada. Contacta a un administrador.',
                ]);
            }

            return $usuario;
        });
    }

    /**
     * Bitácora de acceso (RF-USR-002): registra cada intento de inicio de
     * sesión exitoso o fallido, con usuario (si se conoce), IP y fecha.
     */
    private function registrarBitacoraDeAcceso(): void
    {
        Event::listen(function (Login $evento) {
            BitacoraAuditoria::registrar('login_exitoso', 'User', $evento->user->id);
        });

        Event::listen(function (Failed $evento) {
            $correoIntentado = $evento->credentials[Fortify::username()] ?? null;

            // Fortify no adjunta el modelo en este evento (credenciales inválidas);
            // se busca por correo solo para enlazar el intento a una cuenta existente.
            $usuarioObjetivo = $evento->user ?? ($correoIntentado
                ? User::query()->where('email', $correoIntentado)->first()
                : null);

            BitacoraAuditoria::registrar('login_fallido', 'User', $usuarioObjetivo?->id, [
                'correo_intentado' => $correoIntentado,
            ]);
        });
    }
}
