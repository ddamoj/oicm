<div>
    <div class="mb-10 rounded-xl border border-borde bg-superficie p-6 shadow-suave">
        <div class="relative">
            <label for="busqueda-enlaces-publico" class="sr-only">Buscar enlaces</label>
            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input
                type="search"
                id="busqueda-enlaces-publico"
                wire:model.live.debounce.400ms="busqueda"
                placeholder="Buscar por nombre o descripción…"
                class="w-full rounded-full border border-borde bg-superficie-alterna py-3 pl-11 pr-4 text-sm text-texto
                       placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>
    </div>

    @if ($categorias->isEmpty())
        <x-vacio titulo="Sin enlaces que coincidan" descripcion="Ajusta la búsqueda para ver más resultados.">
            @if ($busqueda !== '')
                <x-boton wire:click="limpiarFiltros" variante="secundario">Quitar búsqueda</x-boton>
            @endif
        </x-vacio>
    @else
        <div class="space-y-14">
            @foreach ($categorias as $categoria)
                <section aria-labelledby="categoria-{{ $categoria->id }}">
                    <h2 id="categoria-{{ $categoria->id }}" class="mb-5 text-xl font-bold tracking-tight text-texto">
                        {{ $categoria->nombre }}
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($categoria->enlaces as $enlace)
                            <x-tarjeta
                                wire:key="enlace-publico-{{ $enlace->id }}"
                                href="{{ $enlace->url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                flotante
                                acento="primario"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-semibold text-texto">{{ $enlace->nombre }}</h3>

                                    <span class="mt-0.5 shrink-0 text-gris" aria-hidden="true">
                                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v9.5c0 .414.336.75.75.75h9.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0113.75 18h-9.5A2.25 2.25 0 012 15.75v-9.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z" clip-rule="evenodd" />
                                            <path fill-rule="evenodd" d="M6.194 12.53a.75.75 0 00-1.06 1.06l7.5 7.5a.75.75 0 001.06-1.06l-7.5-7.5z" clip-rule="evenodd" />
                                            <path fill-rule="evenodd" d="M17.68 4.32a.75.75 0 00-.5-.22h-4.5a.75.75 0 000 1.5h2.69L9.22 11.47a.75.75 0 101.06 1.06L16.43 6.38v2.69a.75.75 0 001.5 0v-4.5a.75.75 0 00-.25-.25z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </div>

                                @if ($enlace->descripcion)
                                    <p class="texto-justificado mt-2 text-sm text-texto-secundario">{{ $enlace->descripcion }}</p>
                                @endif

                                <p class="mt-3 text-xs font-medium uppercase tracking-wide text-primario">
                                    {{ parse_url($enlace->url, PHP_URL_HOST) }}
                                    <span class="sr-only">— se abre en una pestaña nueva</span>
                                </p>
                            </x-tarjeta>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
