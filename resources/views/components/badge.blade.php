@props([
    'variante' => 'neutro', // neutro | primario | acento | exito | error | advertencia
])

@php
    $variantes = [
        'neutro' => 'bg-gris-claro text-texto-secundario',
        'primario' => 'bg-primario-claro text-primario-oscuro',
        'acento' => 'bg-acento/25 text-primario-oscuro',
        'exito' => 'bg-exito-suave text-exito',
        'error' => 'bg-error-suave text-error',
        'advertencia' => 'bg-advertencia-suave text-advertencia',
    ];

    $clases = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold font-sans '
        . ($variantes[$variante] ?? $variantes['neutro']);
@endphp

<span {{ $attributes->merge(['class' => $clases]) }}>
    {{ $slot }}
</span>
