@props([
    'variante' => 'neutro', // neutro | primario | acento | exito | error | advertencia
    'punto' => false, // antepone un indicador circular del color de la variante
])

@php
    $variantes = [
        'neutro' => ['clase' => 'bg-gris-claro text-texto-secundario', 'punto' => 'bg-gris-oscuro'],
        'primario' => ['clase' => 'bg-primario-claro text-primario-oscuro', 'punto' => 'bg-primario'],
        'acento' => ['clase' => 'bg-acento/25 text-primario-oscuro', 'punto' => 'bg-acento-oscuro'],
        'exito' => ['clase' => 'bg-exito-suave text-exito', 'punto' => 'bg-exito'],
        'error' => ['clase' => 'bg-error-suave text-error', 'punto' => 'bg-error'],
        'advertencia' => ['clase' => 'bg-advertencia-suave text-advertencia', 'punto' => 'bg-advertencia'],
    ];

    $paleta = $variantes[$variante] ?? $variantes['neutro'];

    $clases = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold font-sans tracking-wide '
        . $paleta['clase'];
@endphp

<span {{ $attributes->merge(['class' => $clases]) }}>
    @if ($punto)
        <span class="size-1.5 rounded-full {{ $paleta['punto'] }}" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
