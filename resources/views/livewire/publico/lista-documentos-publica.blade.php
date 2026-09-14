<div>
    <div class="mb-10 flex flex-col gap-4 rounded-xl border border-borde bg-superficie p-6 shadow-suave sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <label for="busqueda-documentos-publico" class="sr-only">Buscar documentos</label>
            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input
                type="search"
                id="busqueda-documentos-publico"
                wire:model.live.debounce.400ms="busqueda"
                placeholder="Buscar por nombre o descripción…"
                class="w-full rounded-full border border-borde bg-superficie-alterna py-3 pl-11 pr-4 text-sm text-texto
                       placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>

        <label for="filtro-categoria-documentos-publico" class="sr-only">Filtrar por categoría</label>
        <select id="filtro-categoria-documentos-publico" wire:model.live="filtroCategoria" class="rounded-lg border border-borde bg-superficie-alterna px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
            @endforeach
        </select>
    </div>

    @if ($direccionActiva)
        <div class="mb-6 flex items-center justify-between gap-4 rounded-lg bg-primario-claro px-4 py-3 text-sm text-primario-oscuro">
            <span>Mostrando documentos de <strong>{{ $direccionActiva->nombre }}</strong>.</span>
            <button type="button" wire:click="limpiarFiltros" class="font-semibold underline">Quitar filtro</button>
        </div>
    @endif

    @if ($documentos->isEmpty())
        <x-vacio
            titulo="Sin documentos que coincidan"
            descripcion="No encontramos documentos con esa búsqueda o categoría. Ajusta los filtros e intenta de nuevo."
        >
            @if ($busqueda !== '' || $filtroCategoria !== '' || $filtroDireccion !== '')
                <x-boton wire:click="limpiarFiltros" variante="secundario">Quitar filtros</x-boton>
            @endif
        </x-vacio>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($documentos as $documento)
                <x-tarjeta wire:key="documento-publico-{{ $documento->id }}" acento="primario">
                    <x-slot:icono>
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H8.25M6.75 21h10.5a2.25 2.25 0 002.25-2.25V9.75L15 3H6.75a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006.75 21z" />
                        </svg>
                    </x-slot:icono>

                    <x-badge variante="primario">{{ $documento->categoria?->nombre }}</x-badge>

                    <h2 class="mt-4 text-lg font-bold tracking-tight text-texto">{{ $documento->nombre }}</h2>

                    @if ($documento->descripcion)
                        <p class="texto-justificado mt-2 text-sm leading-relaxed text-texto-secundario">{{ $documento->descripcion }}</p>
                    @endif

                    <div class="mt-4 flex items-center gap-3 text-xs text-texto-secundario">
                        <span class="uppercase">{{ $documento->extension }}</span>
                        <span aria-hidden="true">·</span>
                        <span>{{ number_format($documento->tamano_bytes / 1024 / 1024, 2) }} MB</span>
                        <span aria-hidden="true">·</span>
                        <span>{{ $documento->updated_at->format('d/m/Y') }}</span>
                    </div>

                    <x-boton href="{{ route('documentos.descargar', $documento) }}" variante="secundario" tamano="sm" class="mt-5 w-full">
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v6.638l1.96-2.158a.75.75 0 111.08 1.04l-3.25 3.5a.75.75 0 01-1.08 0l-3.25-3.5a.75.75 0 111.08-1.04l1.96 2.158V3.75A.75.75 0 0110 3zM3.75 13a.75.75 0 01.75.75v1a.75.75 0 00.75.75h9.5a.75.75 0 00.75-.75v-1a.75.75 0 011.5 0v1A2.25 2.25 0 0114.75 17h-9.5A2.25 2.25 0 013 14.75v-1a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                        </svg>
                        Descargar
                    </x-boton>
                </x-tarjeta>
            @endforeach
        </div>

        <x-paginacion :paginador="$documentos" />
    @endif
</div>
