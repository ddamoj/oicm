<x-layouts.autenticacion titulo="Recuperar contraseña">
    <p class="antetitulo">Recuperar acceso</p>
    <h2 class="mt-2 text-2xl font-bold tracking-tight text-texto">¿Olvidaste tu contraseña?</h2>
    <p class="mt-2 text-sm text-texto-secundario">
        Escribe tu correo institucional y te enviaremos un enlace para elegir una nueva contraseña.
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
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

        <x-boton tipo="submit" class="w-full">Enviar enlace de recuperación</x-boton>

        <p class="text-center text-sm">
            <a href="{{ route('login') }}" class="font-semibold text-primario hover:underline">← Volver a iniciar sesión</a>
        </p>
    </form>
</x-layouts.autenticacion>
