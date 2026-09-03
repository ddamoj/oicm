<div>
    <div class="mb-8">
        <label for="busqueda-estrados-publico" class="sr-only">Buscar en los estrados digitales</label>
        <div class="relative">
            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input
                type="search"
                id="busqueda-estrados-publico"
                wire:model.live.debounce.400ms="busqueda"
                placeholder="Buscar por número, expediente o asunto…"
                class="w-full rounded-full border border-borde bg-superficie py-3 pl-11 pr-4 text-sm text-texto
                       placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>
    </div>

    @if ($estrados->isEmpty())
        <x-vacio titulo="Sin estrados que coincidan" descripcion="No encontramos notificaciones con esa búsqueda." />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Número</th>
                            <th class="px-6 py-3 font-semibold">Expediente</th>
                            <th class="px-6 py-3 font-semibold">Asunto</th>
                            <th class="px-6 py-3 font-semibold">Publicación</th>
                            <th class="px-6 py-3 font-semibold text-right">Documento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($estrados as $estrado)
                            <tr wire:key="estrado-publico-{{ $estrado->id }}">
                                <td class="px-6 py-4 font-mono font-semibold text-texto">{{ $estrado->numero }}</td>
                                <td class="px-6 py-4 text-texto-secundario">{{ $estrado->expediente ?? '—' }}</td>
                                <td class="px-6 py-4 text-texto">{{ $estrado->asunto }}</td>
                                <td class="px-6 py-4 text-texto-secundario">{{ $estrado->fecha_publicacion->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <x-boton href="{{ route('estrados.descargar', $estrado) }}" variante="secundario" tamano="sm">
                                        Ver PDF
                                    </x-boton>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-tarjeta>

        <p class="mt-4 text-xs text-texto-secundario">
            Cada estrado conserva una huella digital (SHA-256) del archivo publicado como constancia de integridad.
        </p>

        <x-paginacion :paginador="$estrados" />
    @endif
</div>
