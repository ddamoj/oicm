@props([
    'titulo',
    'subtitulo' => null,
    'alto' => false, // true = hero de portada (más alto); false = hero de página interior
])

<header class="relative overflow-hidden bg-primario text-white {{ $alto ? 'py-24 sm:py-32' : 'py-14 sm:py-20' }}">
    {{-- Greca decorativa sutil: no debe distraer del contenido ni fallar en contraste --}}
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-primario via-primario to-primario-oscuro" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -right-24 -top-24 size-96 rounded-full bg-acento/10 blur-3xl" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl animate-aparecer">
            @isset($etiqueta)
                <div class="mb-4">{{ $etiqueta }}</div>
            @endisset

            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl {{ $alto ? 'sm:text-6xl' : '' }}">
                {{ $titulo }}
            </h1>

            @if ($subtitulo)
                <p class="mt-6 text-lg leading-relaxed text-white/85">
                    {{ $subtitulo }}
                </p>
            @endif

            @if ($slot->isNotEmpty())
                <div class="mt-8">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</header>
