@props([
    'titulo',
    'subtitulo' => null,
    'alto' => false, // true = hero de portada (más alto); false = hero de página interior
])

<header class="relative overflow-hidden bg-primario text-white {{ $alto ? 'py-24 sm:py-32' : 'py-16 sm:py-24' }}">
    {{-- Base tonal: del verde institucional a su variante oscura --}}
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-primario to-primario-oscuro" aria-hidden="true"></div>

    {{-- Greca escalonada como textura de fondo: sello local, discreto --}}
    <div class="greca-patron pointer-events-none absolute inset-0" aria-hidden="true"></div>

    {{-- Resplandor lima detrás del contenido: cálido, no un simple blur genérico --}}
    <div class="pointer-events-none absolute inset-0 [background:radial-gradient(60%_55%_at_18%_0%,rgba(205,222,0,0.16),transparent_70%)]" aria-hidden="true"></div>

    {{-- Hilo de acento inferior, remate de marca --}}
    <div class="linea-acento absolute inset-x-0 bottom-0" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl animate-aparecer">
            @isset($etiqueta)
                <div class="mb-5">{{ $etiqueta }}</div>
            @endisset

            <h1 class="text-5xl font-bold leading-[1.05] tracking-tight sm:text-6xl {{ $alto ? 'sm:text-7xl' : '' }}">
                {{ $titulo }}
            </h1>

            @if ($subtitulo)
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-white/80 sm:text-xl">
                    {{ $subtitulo }}
                </p>
            @endif

            @if ($slot->isNotEmpty())
                <div class="mt-9">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</header>
