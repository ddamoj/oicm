<x-layouts.autenticacion titulo="Iniciar sesión">
    <p class="antetitulo">Iniciar sesión</p>
    <h2 class="mt-2 text-2xl font-bold tracking-tight text-texto">Accede al panel de administración</h2>
    <p class="mt-2 text-sm text-texto-secundario">
        Uso exclusivo del personal administrativo del OICM. Si eres visitante,
        <a href="{{ route('inicio') }}" class="font-semibold text-primario hover:underline">no necesitas iniciar sesión</a>
        para consultar el micrositio.
    </p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-texto">Correo institucional</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="w-full rounded-lg border border-borde bg-superficie px-4 py-3 text-sm text-texto
                       placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                placeholder="nombre@oicm.oaxacadejuarez.gob.mx"
            >
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="block text-sm font-semibold text-texto">Contraseña</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-primario hover:underline">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full rounded-lg border border-borde bg-superficie px-4 py-3 text-sm text-texto
                       focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>

        <label class="flex items-center gap-2 text-sm text-texto-secundario">
            <input type="checkbox" name="remember" class="size-4 rounded border-borde text-primario focus:ring-acento-oscuro">
            Mantener sesión iniciada
        </label>

        <x-boton tipo="submit" class="w-full">Iniciar sesión</x-boton>
    </form>
</x-layouts.autenticacion>
