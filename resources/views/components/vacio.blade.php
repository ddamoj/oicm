@props([
    'titulo' => 'Sin resultados',
    'descripcion' => null,
    'icono' => null,
])

<div {{ $attributes->merge(['class' => 'relative flex flex-col items-center justify-center overflow-hidden rounded-xl border border-dashed border-borde bg-superficie-alterna px-6 py-16 text-center']) }}>
    <x-sello-oicm class="pointer-events-none absolute -right-10 -top-10 size-56 text-primario/[0.05]" />

    <div class="relative mb-4 flex size-14 items-center justify-center rounded-full bg-primario-claro text-primario">
        @if ($icono)
            {{ $icono }}
        @else
            <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @endif
    </div>

    <h3 class="relative text-lg font-semibold tracking-tight text-texto">{{ $titulo }}</h3>

    @if ($descripcion)
        <p class="relative mt-1.5 max-w-sm text-sm text-texto-secundario">{{ $descripcion }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="relative mt-6">{{ $slot }}</div>
    @endif
</div>
