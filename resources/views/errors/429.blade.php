<x-layouts.publico titulo="Demasiados intentos">
    <x-seccion>
        <div class="mx-auto max-w-lg py-8 text-center">
            <p class="font-sans text-sm font-semibold uppercase tracking-wide text-error">Error 429</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Demasiados intentos</h1>
            <p class="mt-4 text-texto-secundario">
                @if (isset($exception) && $exception->getHeaders()['Retry-After'] ?? null)
                    Por seguridad, tu acceso quedó bloqueado temporalmente. Intenta de nuevo en
                    {{ (int) $exception->getHeaders()['Retry-After'] }} segundos.
                @else
                    Por seguridad, tu acceso quedó bloqueado temporalmente. Intenta de nuevo en unos minutos.
                @endif
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <x-boton href="{{ route('inicio') }}">Ir al inicio</x-boton>
            </div>
        </div>
    </x-seccion>
</x-layouts.publico>
