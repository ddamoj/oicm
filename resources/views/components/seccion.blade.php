@props([
    'titulo' => null,
    'descripcion' => null,
    'antetitulo' => null, // texto corto en mayúsculas que antecede al título (voz institucional)
    'ancho' => 'max-w-7xl', // permite angostar el contenedor en páginas de lectura
    'alterna' => false, // fondo gris claro para alternar bandas en la página
])

<section {{ $attributes->merge(['class' => 'py-16 sm:py-24 ' . ($alterna ? 'bg-superficie-alterna' : '')]) }}>
    <div class="mx-auto {{ $ancho }} px-4 sm:px-6 lg:px-8">
        @if ($titulo || $descripcion)
            <div class="mb-12 max-w-3xl">
                @if ($antetitulo)
                    <p class="antetitulo mb-3">{{ $antetitulo }}</p>
                @endif
                @if ($titulo)
                    <h2 class="text-4xl font-bold leading-[1.1] tracking-tight sm:text-5xl">{{ $titulo }}</h2>
                @endif
                @if ($descripcion)
                    <p class="mt-5 text-lg leading-relaxed text-texto-secundario">{{ $descripcion }}</p>
                @endif
            </div>
        @endif

        {{ $slot }}
    </div>
</section>
