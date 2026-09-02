@props([
    'titulo' => null,
    'descripcion' => null,
    'ancho' => 'max-w-7xl', // permite angostar el contenedor en páginas de lectura
    'alterna' => false, // fondo gris claro para alternar bandas en la página
])

<section {{ $attributes->merge(['class' => 'py-16 sm:py-20 ' . ($alterna ? 'bg-superficie-alterna' : '')]) }}>
    <div class="mx-auto {{ $ancho }} px-4 sm:px-6 lg:px-8">
        @if ($titulo || $descripcion)
            <div class="mb-10 max-w-3xl">
                @if ($titulo)
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $titulo }}</h2>
                @endif
                @if ($descripcion)
                    <p class="mt-4 text-lg text-texto-secundario">{{ $descripcion }}</p>
                @endif
            </div>
        @endif

        {{ $slot }}
    </div>
</section>
