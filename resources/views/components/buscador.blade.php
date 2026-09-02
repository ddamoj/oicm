@props([
    'accion' => null,
    'parametro' => 'q',
    'valor' => null,
    'marcador' => 'Buscar...',
    'etiqueta' => 'Buscar',
])

{{-- Formulario GET simple: funciona incluso sin JavaScript --}}
<form
    role="search"
    method="GET"
    action="{{ $accion ?? request()->url() }}"
    {{ $attributes->merge(['class' => 'flex w-full gap-2']) }}
>
    <label for="buscador-{{ $parametro }}" class="sr-only">{{ $etiqueta }}</label>

    <div class="relative flex-1">
        <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
        </svg>

        <input
            type="search"
            id="buscador-{{ $parametro }}"
            name="{{ $parametro }}"
            value="{{ $valor ?? request($parametro) }}"
            placeholder="{{ $marcador }}"
            class="w-full rounded-full border border-borde bg-superficie py-3 pl-11 pr-4 text-sm text-texto
                   placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
        >
    </div>

    <x-boton tipo="submit" variante="primario">
        <span class="sr-only sm:not-sr-only">Buscar</span>
        <svg class="size-4 sm:hidden" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
        </svg>
    </x-boton>
</form>
