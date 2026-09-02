@props([
    'variante' => 'primario', // primario | secundario | fantasma | peligro
    'tamano' => 'md', // sm | md | lg
    'href' => null,
    'tipo' => 'button',
    'cargando' => false,
])

@php
    // Cada variante mapea a las clases de la paleta institucional.
    // Se resuelve aquí para no repetir la lógica en cada vista que use el botón.
    $variantes = [
        'primario' => 'bg-primario text-white hover:bg-primario-oscuro',
        'secundario' => 'bg-transparent text-primario border border-primario hover:bg-primario-claro',
        'fantasma' => 'bg-transparent text-texto hover:bg-gris-claro',
        'peligro' => 'bg-error text-white hover:bg-error/90',
        'acento' => 'bg-acento text-primario-oscuro hover:bg-acento-oscuro',
    ];

    $tamanos = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-4 text-base',
    ];

    $clasesBase = 'inline-flex items-center justify-center gap-2 rounded-full font-sans font-bold tracking-tight '
        . 'transition-all duration-200 ease-institucional disabled:opacity-50 disabled:pointer-events-none '
        . 'active:scale-[0.98]';

    $clases = trim($clasesBase . ' ' . ($variantes[$variante] ?? $variantes['primario']) . ' ' . ($tamanos[$tamano] ?? $tamanos['md']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $tipo }}" {{ $attributes->merge(['class' => $clases]) }} @if ($cargando) disabled aria-busy="true" @endif>
        @if ($cargando)
            <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
