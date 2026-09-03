<div>
    <x-migas :items="['Galerías' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Galería de fotos y videos</h1>
            <p class="mt-1 text-sm text-texto-secundario">Eventos institucionales: capacitaciones, sesiones COCODI, Comité de Obras y entrega-recepción.</p>
        </div>

        <x-boton wire:click="nueva" variante="primario">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Nueva galería
        </x-boton>
    </div>

    <x-tarjeta class="mb-6">
        <div class="relative">
            <label for="busqueda-galerias" class="sr-only">Buscar galerías</label>
            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input
                type="search"
                id="busqueda-galerias"
                wire:model.live.debounce.400ms="busqueda"
                placeholder="Buscar por título…"
                class="w-full rounded-full border border-borde bg-superficie py-3 pl-11 pr-4 text-sm text-texto
                       placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
        </div>
    </x-tarjeta>

    @if ($galerias->isEmpty())
        <x-vacio titulo="Sin galerías que coincidan" descripcion="Ajusta la búsqueda o crea la primera galería del evento." />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Título</th>
                            <th class="px-6 py-3 font-semibold">Fecha del evento</th>
                            <th class="px-6 py-3 font-semibold">Medios</th>
                            <th class="px-6 py-3 font-semibold">Estado</th>
                            <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($galerias as $galeria)
                            <tr wire:key="galeria-{{ $galeria->id }}">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-texto">{{ $galeria->titulo }}</div>
                                    @if ($galeria->descripcion)
                                        <div class="text-xs text-texto-secundario">{{ Str::limit($galeria->descripcion, 60) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-texto-secundario">
                                    {{ $galeria->fecha_evento?->translatedFormat('d \d\e F \d\e Y') ?? 'Sin fecha' }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge variante="primario">{{ $galeria->medios_count }} {{ Str::plural('archivo', $galeria->medios_count) }}</x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($galeria->publicada)
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
                                                if (confirmado) { $wire[metodo]({{ $galeria->id }}); }
                                            },
                                        }"
                                    >
                                        <a href="{{ route('admin.galerias.medios', $galeria) }}" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Gestionar medios de {{ $galeria->titulo }}">
                                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M1 5.25A2.25 2.25 0 013.25 3h13.5A2.25 2.25 0 0119 5.25v9.5A2.25 2.25 0 0116.75 17H3.25A2.25 2.25 0 011 14.75v-9.5zm1.5 5.81v3.69c0 .414.336.75.75.75h13.5a.75.75 0 00.75-.75v-2.69l-2.22-2.219a.75.75 0 00-1.06 0l-1.91 1.909.47.47a.75.75 0 11-1.06 1.06L6.53 8.091a.75.75 0 00-1.06 0l-2.97 2.97zM12 7a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" /></svg>
                                        </a>

                                        <button type="button" wire:click="editar({{ $galeria->id }})" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Editar {{ $galeria->titulo }}">
                                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                        </button>

                                        <button type="button" x-on:click="confirmarYEjecutar('alternarPublicada', '{{ $galeria->publicada ? '¿Retirar' : '¿Mostrar' }} {{ $galeria->titulo }}?', 'El cambio se refleja de inmediato en el grid público.')" class="rounded-full p-2 text-texto-secundario hover:bg-advertencia-suave hover:text-advertencia" aria-label="{{ $galeria->publicada ? 'Retirar' : 'Mostrar' }} {{ $galeria->titulo }}">
                                            @if ($galeria->publicada)
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd" /><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z" /></svg>
                                            @else
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" /><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </button>

                                        <button type="button" x-on:click="confirmarYEjecutar('eliminar', '¿Eliminar {{ $galeria->titulo }}?', 'Esta acción borra también sus archivos y no se puede deshacer.')" class="rounded-full p-2 text-texto-secundario hover:bg-error-suave hover:text-error" aria-label="Eliminar {{ $galeria->titulo }}">
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

        <x-paginacion :paginador="$galerias" />
    @endif

    <livewire:admin.galerias.formulario-galeria />
</div>
