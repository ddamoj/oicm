<div>
    <div class="mb-8 flex flex-col gap-4 rounded-xl border border-borde bg-superficie p-6 shadow-suave sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <label for="busqueda-normatividad-publico" class="sr-only">Buscar en el marco normativo</label>
            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input
                type="search"
                id="busqueda-normatividad-publico"
                wire:model.live.debounce.400ms="busqueda"
                placeholder="Buscar por título…"
                class="w-full rounded-full border border-borde bg-superficie-alterna py-3 pl-11 pr-4 text-sm text-texto
                       placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>

        <div class="flex flex-wrap gap-2" role="tablist" aria-label="Filtrar por ámbito">
            @foreach (['federal' => 'Federal', 'estatal' => 'Estatal', 'municipal' => 'Municipal'] as $clave => $etiqueta)
                <button
                    type="button"
                    role="tab"
                    aria-selected="{{ $ambito === $clave ? 'true' : 'false' }}"
                    wire:click="filtrarAmbito('{{ $clave }}')"
                    class="rounded-full px-4 py-2 text-sm font-semibold transition-colors {{ $ambito === $clave ? 'bg-primario text-white' : 'bg-superficie-alterna text-texto-secundario hover:text-primario' }}"
                >
                    {{ $etiqueta }}
                </button>
            @endforeach
        </div>
    </div>

    @if ($normatividad->isEmpty())
        <x-vacio
            titulo="Sin ordenamientos que coincidan"
            descripcion="No encontramos resultados con esa búsqueda o ámbito. Ajusta los filtros e intenta de nuevo."
        >
            @if ($busqueda !== '' || $ambito !== '')
                <x-boton wire:click="limpiarFiltros" variante="secundario">Quitar filtros</x-boton>
            @endif
        </x-vacio>
    @else
        <div class="space-y-4">
            @foreach ($normatividad as $item)
                <x-tarjeta wire:key="normatividad-publico-{{ $item->id }}">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <x-badge variante="primario">{{ ucfirst($item->ambito) }}</x-badge>
                            <h3 class="mt-3 text-lg font-bold tracking-tight text-texto">{{ $item->titulo }}</h3>
                            @if ($item->descripcion)
                                <p class="mt-1 text-sm leading-relaxed text-texto-secundario">{{ $item->descripcion }}</p>
                            @endif
                        </div>

                        @if ($item->documento_url)
                            <x-boton href="{{ $item->documento_url }}" target="_blank" rel="noopener noreferrer" variante="secundario" tamano="sm" class="shrink-0">
                                Consultar
                            </x-boton>
                        @endif
                    </div>

                    <dl class="mt-4 grid gap-x-6 gap-y-1 border-t border-borde pt-4 text-xs text-texto-secundario sm:grid-cols-3">
                        @if ($item->medio_publicacion)
                            <div><dt class="font-semibold">Medio</dt><dd>{{ $item->medio_publicacion }}</dd></div>
                        @endif
                        @if ($item->fecha_publicacion)
                            <div><dt class="font-semibold">Publicación</dt><dd>{{ $item->fecha_publicacion->format('d/m/Y') }}</dd></div>
                        @endif
                        @if ($item->fecha_ultima_reforma)
                            <div><dt class="font-semibold">Última reforma</dt><dd>{{ $item->fecha_ultima_reforma->format('d/m/Y') }}</dd></div>
                        @endif
                    </dl>
                </x-tarjeta>
            @endforeach
        </div>

        <x-paginacion :paginador="$normatividad" />
    @endif
</div>
