@props([
    'href' => null,
    'flotante' => false, // eleva la sombra al pasar el cursor (para tarjetas clicables)
    'acento' => 'primario', // primario | acento | neutro — color del remate superior y del icono
    'icono' => null, // slot HTML (svg) opcional, se muestra en un círculo de color sobre el contenido
])

@php
    $acentos = [
        'primario' => ['borde' => 'bg-primario', 'circulo' => 'bg-primario-claro text-primario'],
        'acento' => ['borde' => 'bg-acento', 'circulo' => 'bg-acento/25 text-primario-oscuro'],
        'neutro' => ['borde' => 'bg-gris', 'circulo' => 'bg-gris-claro text-texto-secundario'],
    ];

    $paleta = $acentos[$acento] ?? $acentos['primario'];

    $clases = 'group relative block overflow-hidden rounded-xl border border-borde bg-superficie p-6 shadow-suave transition-all duration-200 ease-institucional'
        . ($flotante || $href ? ' hover:-translate-y-1 hover:border-primario/25 hover:shadow-flotante' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>
        <span class="pointer-events-none absolute inset-x-0 top-0 h-1 {{ $paleta['borde'] }}" aria-hidden="true"></span>
        @if ($icono)
            <span class="mb-4 flex size-11 items-center justify-center rounded-full {{ $paleta['circulo'] }} transition-transform duration-200 ease-institucional group-hover:scale-105">
                {{ $icono }}
            </span>
        @endif
        {{ $slot }}
    </a>
@else
    <div {{ $attributes->merge(['class' => $clases]) }}>
        <span class="pointer-events-none absolute inset-x-0 top-0 h-1 {{ $paleta['borde'] }}" aria-hidden="true"></span>
        @if ($icono)
            <span class="mb-4 flex size-11 items-center justify-center rounded-full {{ $paleta['circulo'] }}">
                {{ $icono }}
            </span>
        @endif
        {{ $slot }}
    </div>
@endif
