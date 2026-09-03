<div>
    <div class="mb-10 flex flex-col gap-4 rounded-xl border border-borde bg-superficie p-6 shadow-suave lg:flex-row lg:items-end">
        <div class="relative flex-1">
            <label for="busqueda-noticias-publico" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-texto-secundario">Palabra clave</label>
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input
                    type="search"
                    id="busqueda-noticias-publico"
                    wire:model.live.debounce.400ms="busqueda"
                    placeholder="Buscar por título o contenido…"
                    class="w-full rounded-full border border-borde bg-superficie-alterna py-3 pl-11 pr-4 text-sm text-texto
                           placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
            </div>
        </div>

        <div>
            <label for="noticias-desde" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-texto-secundario">Desde</label>
            <input
                type="date"
                id="noticias-desde"
                wire:model.live="desde"
                class="rounded-lg border border-borde bg-superficie-alterna px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>

        <div>
            <label for="noticias-hasta" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-texto-secundario">Hasta</label>
            <input
                type="date"
                id="noticias-hasta"
                wire:model.live="hasta"
                class="rounded-lg border border-borde bg-superficie-alterna px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>

        @if ($busqueda !== '' || $desde !== '' || $hasta !== '')
            <x-boton wire:click="limpiarFiltros" variante="secundario" tamano="sm">Quitar filtros</x-boton>
        @endif
    </div>

    @if ($noticias->isEmpty())
        <x-vacio
            titulo="Sin noticias que coincidan"
            descripcion="No encontramos noticias con esa búsqueda o rango de fechas. Ajusta los filtros e intenta de nuevo."
        >
            @if ($busqueda !== '' || $desde !== '' || $hasta !== '')
                <x-boton wire:click="limpiarFiltros" variante="secundario">Quitar filtros</x-boton>
            @endif
        </x-vacio>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($noticias as $noticia)
                <x-tarjeta wire:key="noticia-{{ $noticia->id }}" href="{{ route('noticias.mostrar', $noticia) }}" flotante class="!p-0 overflow-hidden">
                    <div class="aspect-[3/2] w-full overflow-hidden bg-gris-claro">
                        @if ($noticia->urlMiniatura())
                            <img
                                src="{{ $noticia->urlMiniatura() }}"
                                alt="{{ $noticia->imagen_alt }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition-transform duration-300 ease-institucional group-hover:scale-105"
                            >
                        @else
                            <div class="flex h-full w-full items-center justify-center text-gris">
                                <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M4.5 3.75h15A2.25 2.25 0 0121.75 6v12A2.25 2.25 0 0119.5 20.25h-15A2.25 2.25 0 012.25 18V6a2.25 2.25 0 012.25-2.25z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-texto-secundario">
                            {{ $noticia->publicado_en?->translatedFormat('d \d\e F \d\e Y') }}
                        </p>
                        <h3 class="mt-2 text-lg font-bold tracking-tight text-texto">{{ $noticia->titulo }}</h3>

                        @if ($noticia->resumen)
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-texto-secundario">{{ $noticia->resumen }}</p>
                        @endif
                    </div>
                </x-tarjeta>
            @endforeach
        </div>

        <x-paginacion :paginador="$noticias" />
    @endif
</div>
