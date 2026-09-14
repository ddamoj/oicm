@props([
    'titulo',
    'subtitulo' => null,
    'resaltar' => null, // fragmento literal de $titulo a destacar en acento (uso editorial, no de usuario)
    'alto' => false, // true = hero de portada (más alto); false = hero de página interior
])

@php
    // Envuelve el fragmento a destacar en un span de acento. $titulo es
    // siempre texto de autor (nunca entrada de usuario), por eso es seguro
    // construir el HTML aquí en vez de dejarlo solo como prop escapada.
    $tituloHtml = e($titulo);

    if ($resaltar && str_contains($titulo, $resaltar)) {
        $tituloHtml = str_replace(
            e($resaltar),
            '<span class="text-acento">' . e($resaltar) . '</span>',
            $tituloHtml
        );
    }
@endphp

<header class="aurora-oicm relative overflow-hidden text-white {{ $alto ? 'py-28 sm:py-36' : 'py-16 sm:py-24' }}">
    {{-- Sello de control: única marca de agua, sangrada fuera del lienzo —
         nunca un patrón repetido. Firma visual propia del OICM. --}}
    <x-sello-oicm class="pointer-events-none absolute -right-24 -bottom-28 size-[26rem] text-white/[0.06] sm:size-[34rem]" />

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl animate-aparecer">
            @isset($etiqueta)
                <div class="mb-5">{{ $etiqueta }}</div>
            @else
                <p class="mb-5 inline-flex items-center gap-2 font-sans text-xs font-bold uppercase tracking-[0.2em] text-acento">
                    <span class="size-1.5 rounded-full bg-acento" aria-hidden="true"></span>
                    Órgano Interno de Control Municipal
                </p>
            @endisset

            {{-- El color se declara aquí de forma explícita: la regla base de
                 app.css pinta todo encabezado con `text-texto` (casi negro) y
                 ese color gana sobre el `text-white` heredado del <header>. --}}
            <h1 class="text-balance text-5xl font-bold leading-[1.02] tracking-tight text-white sm:text-6xl {{ $alto ? 'lg:text-7xl' : '' }}">
                {!! $tituloHtml !!}
            </h1>

            @if ($subtitulo)
                <p class="mt-7 max-w-2xl text-lg leading-relaxed text-white/75 sm:text-xl">
                    {{ $subtitulo }}
                </p>
            @endif

            @if ($slot->isNotEmpty())
                <div class="mt-10">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</header>
