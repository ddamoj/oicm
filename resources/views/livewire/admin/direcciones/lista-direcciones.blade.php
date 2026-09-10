<div>
    <x-migas :items="['Direcciones' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Direcciones</h1>
            <p class="mt-1 text-sm text-texto-secundario">
                Estructura orgánica del OICM. Alimenta el organigrama de «Quiénes somos» y la página pública de cada Dirección.
            </p>
        </div>

        <x-boton wire:click="nuevo" variante="primario">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Nueva Dirección
        </x-boton>
    </div>

    <x-tarjeta class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <label for="busqueda-direcciones" class="sr-only">Buscar direcciones</label>
                <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input
                    type="search"
                    id="busqueda-direcciones"
                    wire:model.live.debounce.400ms="busqueda"
                    placeholder="Buscar por nombre, siglas o descripción…"
                    class="w-full rounded-full border border-borde bg-superficie py-3 pl-11 pr-4 text-sm text-texto
                           placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
            </div>

            <select wire:model.live="filtroEstado" class="rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
                <option value="">Todos los estados</option>
                <option value="activa">Visibles</option>
                <option value="inactiva">Retiradas</option>
            </select>
        </div>
    </x-tarjeta>

    @if ($direcciones->isEmpty())
        <x-vacio titulo="Sin direcciones que coincidan" descripcion="Ajusta la búsqueda o los filtros para ver más resultados." />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Dirección</th>
                            <th class="px-6 py-3 font-semibold">Departamentos</th>
                            <th class="px-6 py-3 font-semibold">Contenido asociado</th>
                            <th class="px-6 py-3 font-semibold">Orden</th>
                            <th class="px-6 py-3 font-semibold">Estado</th>
                            <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($direcciones as $direccion)
                            <tr wire:key="direccion-{{ $direccion->id }}">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-texto">{{ $direccion->nombre }}</div>
                                    @if ($direccion->siglas)
                                        <div class="text-xs font-semibold uppercase tracking-wide text-texto-secundario">{{ $direccion->siglas }}</div>
                                    @endif
                                    <div class="mt-0.5 text-xs text-gris-oscuro">/direcciones/{{ $direccion->clave }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <a
                                        href="{{ route('admin.direcciones.departamentos', $direccion) }}"
                                        class="inline-flex items-center gap-1.5 text-texto-secundario underline decoration-dotted hover:text-primario"
                                    >
                                        {{ $direccion->departamentos_count }}
                                        {{ $direccion->departamentos_count === 1 ? 'departamento' : 'departamentos' }}
                                    </a>
                                </td>

                                <td class="px-6 py-4 text-xs text-texto-secundario">
                                    {{ $direccion->documentos_count }} doc. ·
                                    {{ $direccion->paginas_count }} pág. ·
                                    {{ $direccion->contactos_count }} cont.
                                </td>

                                <td class="px-6 py-4 text-texto-secundario">{{ $direccion->orden }}</td>

                                <td class="px-6 py-4">
                                    @if ($direccion->activa)
                                        <x-badge variante="exito" :punto="true">Visible</x-badge>
                                    @else
                                        <x-badge variante="neutro" :punto="true">Retirada</x-badge>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                        x-data="{
                                            async confirmarYEjecutar(metodo, titulo, texto) {
                                                const confirmado = await window.alertas.confirmar({ titulo, texto });
                                                if (confirmado) { $wire[metodo]({{ $direccion->id }}); }
                                            },
                                        }"
                                    >
                                        <a href="{{ route('admin.direcciones.departamentos', $direccion) }}" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Departamentos de {{ $direccion->nombre }}">
                                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 4.25A2.25 2.25 0 014.25 2h11.5A2.25 2.25 0 0118 4.25v11.5A2.25 2.25 0 0115.75 18H4.25A2.25 2.25 0 012 15.75V4.25zm3 1.5a.75.75 0 000 1.5h10a.75.75 0 000-1.5H5zm0 3.5a.75.75 0 000 1.5h10a.75.75 0 000-1.5H5zm0 3.5a.75.75 0 000 1.5h6a.75.75 0 000-1.5H5z" /></svg>
                                        </a>

                                        <button type="button" wire:click="editar({{ $direccion->id }})" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Editar {{ $direccion->nombre }}">
                                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                        </button>

                                        <button type="button" x-on:click="confirmarYEjecutar('alternarActiva', '{{ $direccion->activa ? '¿Retirar' : '¿Mostrar' }} {{ $direccion->nombre }}?', 'El cambio se refleja de inmediato en el organigrama público.')" class="rounded-full p-2 text-texto-secundario hover:bg-advertencia-suave hover:text-advertencia" aria-label="{{ $direccion->activa ? 'Retirar' : 'Mostrar' }} {{ $direccion->nombre }}">
                                            @if ($direccion->activa)
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd" /><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z" /></svg>
                                            @else
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" /><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </button>

                                        <button type="button" x-on:click="confirmarYEjecutar('eliminar', '¿Eliminar {{ $direccion->nombre }}?', 'Se eliminarán también sus departamentos. Esta acción no se puede deshacer.')" class="rounded-full p-2 text-texto-secundario hover:bg-error-suave hover:text-error" aria-label="Eliminar {{ $direccion->nombre }}">
                                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-tarjeta>

        <x-paginacion :paginador="$direcciones" />
    @endif

    <livewire:admin.direcciones.formulario-direccion />
</div>
