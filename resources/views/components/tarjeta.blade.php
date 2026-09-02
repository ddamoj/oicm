@props([
    'href' => null,
    'flotante' => false, // eleva la sombra al pasar el cursor (para tarjetas clicables)
])

@php
    $clases = 'block rounded-lg bg-superficie border border-borde p-6 shadow-suave transition-all duration-200 ease-institucional'
        . ($flotante || $href ? ' hover:-translate-y-0.5 hover:shadow-flotante' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>
        {{ $slot }}
    </a>
@else
    <div {{ $attributes->merge(['class' => $clases]) }}>
        {{ $slot }}
    </div>
@endif
