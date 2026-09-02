@props([
    'href' => null,
    'flotante' => false, // eleva la sombra al pasar el cursor (para tarjetas clicables)
    'acento' => 'primario', // primario | acento | neutro — color del icono y del resplandor al pasar el cursor
    'icono' => null, // slot HTML (svg) opcional, se muestra en un círculo de color sobre el contenido
])

@php
    $acentos = [
        'primario' => [
            'circulo' => 'bg-primario-claro text-primario',
            'resplandor' => 'hover:shadow-[0_28px_56px_-30px_rgba(38,91,77,0.55)] hover:border-primario/25',
        ],
        'acento' => [
            'circulo' => 'bg-acento/20 text-primario-oscuro',
            'resplandor' => 'hover:shadow-[0_28px_56px_-30px_rgba(168,184,0,0.5)] hover:border-acento-oscuro/30',
        ],
        'neutro' => [
            'circulo' => 'bg-gris-claro text-texto-secundario',
            'resplandor' => 'hover:shadow-[0_28px_56px_-30px_rgba(90,95,92,0.35)] hover:border-gris/40',
        ],
    ];

    $paleta = $acentos[$acento] ?? $acentos['primario'];

    $clases = 'group block rounded-xl border border-borde bg-superficie p-6 shadow-suave transition-all duration-300 ease-institucional'
        . ($flotante || $href ? ' hover:-translate-y-1 ' . $paleta['resplandor'] : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>
        @if ($icono)
            <span class="mb-4 flex size-11 items-center justify-center rounded-full {{ $paleta['circulo'] }} transition-transform duration-300 ease-institucional group-hover:scale-110">
                {{ $icono }}
            </span>
        @endif
        {{ $slot }}
    </a>
@else
    <div {{ $attributes->merge(['class' => $clases]) }}>
        @if ($icono)
            <span class="mb-4 flex size-11 items-center justify-center rounded-full {{ $paleta['circulo'] }}">
                {{ $icono }}
            </span>
        @endif
        {{ $slot }}
    </div>
@endif
