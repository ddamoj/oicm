<div>
    <x-migas :items="['Páginas institucionales' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Contenido institucional</h1>
            <p class="mt-1 text-sm text-texto-secundario">"Quiénes somos" y las páginas propias de cada Dirección, con historial de versiones.</p>
        </div>

        <x-boton wire:click="nuevo" variante="primario">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Nueva página
        </x-boton>
    </div>

    <x-tarjeta class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <label for="busqueda-paginas" class="sr-only">Buscar páginas</label>
                <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input
                    type="search"
                    id="busqueda-paginas"
                    wire:model.live.debounce.400ms="busqueda"
                    placeholder="Buscar por título…"
                    class="w-full rounded-full border border-borde bg-superficie py-3 pl-11 pr-4 text-sm text-texto
                           placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
            </div>

            <select wire:model.live="filtroEstatus" class="rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
                <option value="">Todos los estatus</option>
                <option value="publicada">Publicadas</option>
                <option value="borrador">Borrador</option>
            </select>
        </div>
    </x-tarjeta>

    @if ($paginas->isEmpty())
        <x-vacio titulo="Sin páginas que coincidan" descripcion="Ajusta la búsqueda o los filtros para ver más resultados." />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Título</th>
                            <th class="px-6 py-3 font-semibold">Dirección</th>
                            <th class="px-6 py-3 font-semibold">Estatus</th>
                            <th class="px-6 py-3 font-semibold">Actualizada</th>
                            <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($paginas as $pagina)
                            <tr wire:key="pagina-{{ $pagina->id }}">
                                <td class="px-6 py-4 font-medium text-texto">{{ $pagina->titulo }}</td>
                                <td class="px-6 py-4 text-texto-secundario">{{ $pagina->direccion?->nombre ?? 'General' }}</td>
                                <td class="px-6 py-4">
                                    @if ($pagina->estatus === 'publicada')
                                        <x-badge variante="exito" :punto="true">Publicada</x-badge>
                                    @else
                                        <x-badge variante="neutro" :punto="true">Borrador</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-texto-secundario">{{ $pagina->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                        x-data="{
                                            async confirmarYEjecutar(metodo, titulo, texto) {
                                                const confirmado = await window.alertas.confirmar({ titulo, texto });
                                                if (confirmado) { $wire[metodo]({{ $pagina->id }}); }
                                            },
                                        }"
                                    >
                                        <x-tooltip texto="Historial de versiones">
                                            <button type="button" wire:click="verHistorial({{ $pagina->id }})" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Historial de {{ $pagina->titulo }}">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a8 8 0 106.32 12.906.75.75 0 00-1.18-.928A6.5 6.5 0 1116.5 10a.75.75 0 001.5 0 8 8 0 00-8-8zM10.75 6a.75.75 0 00-1.5 0v4c0 .2.079.391.22.53l2.5 2.5a.75.75 0 101.06-1.06L10.75 9.69V6z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </x-tooltip>

                                        <x-tooltip texto="Editar">
                                            <button type="button" wire:click="editar({{ $pagina->id }})" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Editar {{ $pagina->titulo }}">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                            </button>
                                        </x-tooltip>

                                        @if ($pagina->estatus === 'publicada')
                                            <x-tooltip texto="Retirar del sitio público">
                                                <button type="button" x-on:click="confirmarYEjecutar('despublicar', '¿Retirar {{ $pagina->titulo }} del sitio público?', 'La página pasará a borrador y podrás editarla y republicarla cuando quieras.')" class="rounded-full p-2 text-texto-secundario hover:bg-advertencia-suave hover:text-advertencia" aria-label="Retirar {{ $pagina->titulo }}">
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd" /><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z" /></svg>
                                                </button>
                                            </x-tooltip>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-tarjeta>

        <x-paginacion :paginador="$paginas" />
    @endif

    <livewire:admin.paginas.formulario-pagina-institucional />
    <livewire:admin.paginas.historial-pagina />
</div>
