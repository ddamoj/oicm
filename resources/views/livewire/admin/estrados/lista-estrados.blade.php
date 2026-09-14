<div>
    <x-migas :items="['Estrados digitales' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Estrados digitales — DRACS</h1>
            <p class="mt-1 text-sm text-texto-secundario">Notificaciones numeradas con constancia de publicación (fecha/hora y huella del archivo).</p>
        </div>

        <x-boton wire:click="nuevo" variante="primario">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Publicar estrado
        </x-boton>
    </div>

    <x-tarjeta class="mb-6">
        <div class="relative">
            <label for="busqueda-estrados" class="sr-only">Buscar estrados</label>
            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input
                type="search"
                id="busqueda-estrados"
                wire:model.live.debounce.400ms="busqueda"
                placeholder="Buscar por número, expediente o asunto…"
                class="w-full rounded-full border border-borde bg-superficie py-3 pl-11 pr-4 text-sm text-texto
                       placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>
    </x-tarjeta>

    @if ($estrados->isEmpty())
        <x-vacio titulo="Sin estrados que coincidan" descripcion="Ajusta la búsqueda para ver más resultados." />
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
                            <th class="px-6 py-3 font-semibold">Estado</th>
                            <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($estrados as $estrado)
                            <tr wire:key="estrado-{{ $estrado->id }}">
                                <td class="px-6 py-4 font-mono font-semibold text-texto">{{ $estrado->numero }}</td>
                                <td class="px-6 py-4 text-texto-secundario">{{ $estrado->expediente ?? '—' }}</td>
                                <td class="px-6 py-4 text-texto">{{ Str::limit($estrado->asunto, 60) }}</td>
                                <td class="px-6 py-4 text-texto-secundario">{{ $estrado->fecha_publicacion->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    @if ($estrado->activo)
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
                                                if (confirmado) { $wire[metodo]({{ $estrado->id }}); }
                                            },
                                        }"
                                    >
                                        <x-tooltip texto="{{ $estrado->activo ? 'Retirar del sitio público' : 'Mostrar en el sitio público' }}">
                                            <button type="button" x-on:click="confirmarYEjecutar('alternarActivo', '{{ $estrado->activo ? '¿Retirar' : '¿Mostrar' }} el estrado {{ $estrado->numero }}?', 'El archivo y el registro se conservan como constancia; solo cambia su visibilidad pública.')" class="rounded-full p-2 text-texto-secundario hover:bg-advertencia-suave hover:text-advertencia" aria-label="{{ $estrado->activo ? 'Retirar' : 'Mostrar' }} estrado {{ $estrado->numero }}">
                                                @if ($estrado->activo)
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd" /><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z" /></svg>
                                                @else
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" /><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                                @endif
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

        <x-paginacion :paginador="$estrados" />
    @endif

    <livewire:admin.estrados.formulario-estrado />
</div>
