<div>
    <x-migas :items="['Enlaces' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Enlaces de interés</h1>
            <p class="mt-1 text-sm text-texto-secundario">Directorio de enlaces externos institucionales, agrupado por categoría.</p>
        </div>

        <x-boton wire:click="nuevo" variante="primario">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Nuevo enlace
        </x-boton>
    </div>

    <x-tarjeta class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <label for="busqueda-enlaces" class="sr-only">Buscar enlaces</label>
                <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input
                    type="search"
                    id="busqueda-enlaces"
                    wire:model.live.debounce.400ms="busqueda"
                    placeholder="Buscar por nombre, descripción o URL…"
                    class="w-full rounded-full border border-borde bg-superficie py-3 pl-11 pr-4 text-sm text-texto
                           placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
            </div>

            <select wire:model.live="filtroCategoria" class="rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>

            <select wire:model.live="filtroEstado" class="rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
                <option value="">Todos los estados</option>
                <option value="activo">Visibles</option>
                <option value="inactivo">Retirados</option>
            </select>
        </div>
    </x-tarjeta>

    @if ($enlaces->isEmpty())
        <x-vacio titulo="Sin enlaces que coincidan" descripcion="Ajusta la búsqueda o los filtros para ver más resultados." />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Nombre</th>
                            <th class="px-6 py-3 font-semibold">Categoría</th>
                            <th class="px-6 py-3 font-semibold">URL</th>
                            <th class="px-6 py-3 font-semibold">Estado</th>
                            <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($enlaces as $enlace)
                            <tr wire:key="enlace-{{ $enlace->id }}">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-texto">{{ $enlace->nombre }}</div>
                                    @if ($enlace->descripcion)
                                        <div class="text-xs text-texto-secundario">{{ Str::limit($enlace->descripcion, 60) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge variante="primario">{{ $enlace->categoria?->nombre }}</x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ $enlace->url }}" target="_blank" rel="noopener noreferrer" class="text-texto-secundario underline decoration-dotted hover:text-primario">
                                        {{ Str::limit($enlace->url, 40) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($enlace->activo)
                                        <x-badge variante="exito" :punto="true">Visible</x-badge>
                                    @else
                                        <x-badge variante="neutro" :punto="true">Retirado</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                        x-data="{
                                            async confirmarYEjecutar(metodo, titulo, texto) {
                                                const confirmado = await window.alertas.confirmar({ titulo, texto });
                                                if (confirmado) { $wire[metodo]({{ $enlace->id }}); }
                                            },
                                        }"
                                    >
                                        <x-tooltip texto="Editar">
                                            <button type="button" wire:click="editar({{ $enlace->id }})" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Editar {{ $enlace->nombre }}">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                            </button>
                                        </x-tooltip>

                                        <x-tooltip texto="{{ $enlace->activo ? 'Retirar del sitio público' : 'Mostrar en el sitio público' }}">
                                            <button type="button" x-on:click="confirmarYEjecutar('alternarActivo', '{{ $enlace->activo ? '¿Retirar' : '¿Mostrar' }} {{ $enlace->nombre }}?', 'El cambio se refleja de inmediato en el directorio público.')" class="rounded-full p-2 text-texto-secundario hover:bg-advertencia-suave hover:text-advertencia" aria-label="{{ $enlace->activo ? 'Retirar' : 'Mostrar' }} {{ $enlace->nombre }}">
                                                @if ($enlace->activo)
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd" /><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z" /></svg>
                                                @else
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" /><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                                @endif
                                            </button>
                                        </x-tooltip>

                                        <x-tooltip texto="Eliminar">
                                            <button type="button" x-on:click="confirmarYEjecutar('eliminar', '¿Eliminar {{ $enlace->nombre }}?', 'Esta acción no se puede deshacer.')" class="rounded-full p-2 text-texto-secundario hover:bg-error-suave hover:text-error" aria-label="Eliminar {{ $enlace->nombre }}">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </x-tooltip>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-tarjeta>

        <x-paginacion :paginador="$enlaces" />
    @endif

    <livewire:admin.enlaces.formulario-enlace />
</div>
