@props([
    'nombre' => 'modal',
    'titulo' => null,
    'maxAncho' => 'lg', // sm | md | lg | xl
])

@php
    $anchos = ['sm' => 'sm:max-w-sm', 'md' => 'sm:max-w-md', 'lg' => 'sm:max-w-lg', 'xl' => 'sm:max-w-2xl'];
@endphp

{{-- Modal accesible: trampa de foco básica, cierre con Escape y overlay --}}
<div
    x-data="{ abierto: false }"
    x-on:abrir-modal.window="if ($event.detail.nombre === '{{ $nombre }}') abierto = true"
    x-on:cerrar-modal.window="if (!$event.detail || $event.detail.nombre === '{{ $nombre }}') abierto = false"
    x-on:keydown.escape.window="abierto = false"
    x-show="abierto"
    x-cloak
    class="relative z-50"
    role="dialog"
    aria-modal="true"
    @if ($titulo) aria-labelledby="modal-titulo-{{ $nombre }}" @endif
>
    <div
        x-show="abierto"
        x-transition:enter="ease-institucional duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-institucional duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-texto/50"
        x-on:click="abierto = false"
        aria-hidden="true"
    ></div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div
            x-show="abierto"
            x-transition:enter="ease-institucional duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-institucional duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full {{ $anchos[$maxAncho] ?? $anchos['lg'] }} rounded-xl bg-superficie p-6 shadow-flotante"
            x-on:click.stop
        >
            <div class="flex items-start justify-between gap-4">
                @if ($titulo)
                    <h2 id="modal-titulo-{{ $nombre }}" class="text-xl font-semibold text-texto">{{ $titulo }}</h2>
                @endif

                <button
                    type="button"
                    x-on:click="abierto = false"
                    class="ml-auto rounded-full p-1.5 text-gris-oscuro hover:bg-gris-claro hover:text-texto"
                    aria-label="Cerrar"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>

            <div class="mt-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
