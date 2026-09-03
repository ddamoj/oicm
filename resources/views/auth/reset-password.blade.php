<x-layouts.autenticacion titulo="Restablecer contraseña">
    <p class="antetitulo">Recuperar acceso</p>
    <h2 class="mt-2 text-2xl font-bold tracking-tight text-texto">Elige tu nueva contraseña</h2>
    <p class="mt-2 text-sm text-texto-secundario">
        Mínimo 10 caracteres, combinando mayúsculas, minúsculas, números y símbolos.
    </p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-texto">Correo institucional</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
                class="w-full rounded-lg border border-borde bg-superficie px-4 py-3 text-sm text-texto
                       focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold text-texto">Nueva contraseña</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="w-full rounded-lg border border-borde bg-superficie px-4 py-3 text-sm text-texto
                       focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-texto">Confirmar contraseña</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="w-full rounded-lg border border-borde bg-superficie px-4 py-3 text-sm text-texto
                       focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>

        <x-boton tipo="submit" class="w-full">Restablecer contraseña</x-boton>
    </form>
</x-layouts.autenticacion>
