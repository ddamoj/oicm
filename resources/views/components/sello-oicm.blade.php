{{--
    Emblema propio del OICM: un sello/dial de verificación (anillos
    concéntricos, marcas de medición y una palomita central) — evoca
    control, auditoría y cumplimiento sin tomar prestada iconografía de
    otros proyectos. Se usa como marca de agua discreta (hero, footer,
    estados vacíos), siempre en una sola instancia, nunca como patrón
    repetido. El color se hereda de `currentColor` vía clases de texto.
--}}
@props([
    'class' => 'size-full',
])

<svg viewBox="0 0 400 400" fill="none" {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    <circle cx="200" cy="200" r="150" stroke="currentColor" stroke-width="1.5" />
    <circle cx="200" cy="200" r="90" stroke="currentColor" stroke-width="1.5" />

    {{-- Marcas de medición, cada 30°: el gesto de un instrumento de control --}}
    <line x1="200" y1="50" x2="200" y2="36" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="275" y1="70.1" x2="282" y2="57.9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="329.9" y1="125" x2="342.1" y2="118" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="350" y1="200" x2="364" y2="200" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="329.9" y1="275" x2="342.1" y2="282" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="275" y1="329.9" x2="282" y2="342.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="200" y1="350" x2="200" y2="364" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="125" y1="329.9" x2="118" y2="342.1" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="70.1" y1="275" x2="57.9" y2="282" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="50" y1="200" x2="36" y2="200" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="70.1" y1="125" x2="57.9" y2="118" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
    <line x1="125" y1="70.1" x2="118" y2="57.9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />

    <path d="M155 202 L188 235 L253 158" stroke="currentColor" stroke-width="11" stroke-linecap="round" stroke-linejoin="round" />
</svg>
