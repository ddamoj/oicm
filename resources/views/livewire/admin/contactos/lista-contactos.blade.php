<div>
    <x-migas :items="['Contacto' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Directorio de contacto</h1>
            <p class="mt-1 text-sm text-texto-secundario">Domicilio, teléfono, horario, mapa y canal de quejas y denuncias por Dirección.</p>
        </div>

        <x-boton wire:click="nuevo" variante="primario">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Nuevo contacto
        </x-boton>
    </div>

    @if ($contactos->isEmpty())
        <x-vacio titulo="Aún no hay contactos" descripcion="Agrega el primer contacto del directorio con el botón de arriba." />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Área</th>
                            <th class="px-6 py-3 font-semibold">Dirección</th>
                            <th class="px-6 py-3 font-semibold">Teléfono / correo</th>
                            <th class="px-6 py-3 font-semibold">Mapa</th>
                            <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($contactos as $contacto)
                            <tr wire:key="contacto-{{ $contacto->id }}">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-texto">{{ $contacto->nombre_area }}</div>
                                    @if ($contacto->canal_quejas_denuncias)
                                        <x-badge variante="advertencia" :punto="true" class="mt-1">Quejas y denuncias</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-texto-secundario">{{ $contacto->direccion?->nombre ?? 'General' }}</td>
                                <td class="px-6 py-4 text-texto-secundario">
                                    <div>{{ $contacto->telefono ?? '—' }}</div>
                                    <div class="text-xs">{{ $contacto->correo ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($contacto->tieneMapa())
                                        <x-badge variante="exito" :punto="true">Con coordenadas</x-badge>
                                    @else
                                        <x-badge variante="neutro" :punto="true">Sin coordenadas</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                        x-data="{
                                            async confirmarYEjecutar(metodo, titulo, texto) {
                                                const confirmado = await window.alertas.confirmar({ titulo, texto });
                                                if (confirmado) { $wire[metodo]({{ $contacto->id }}); }
                                            },
                                        }"
                                    >
                                        <x-tooltip texto="Editar">
                                            <button type="button" wire:click="editar({{ $contacto->id }})" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Editar {{ $contacto->nombre_area }}">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                            </button>
                                        </x-tooltip>

                                        <x-tooltip texto="Eliminar">
                                            <button type="button" x-on:click="confirmarYEjecutar('eliminar', '¿Eliminar {{ $contacto->nombre_area }}?', 'Esta acción no se puede deshacer.')" class="rounded-full p-2 text-texto-secundario hover:bg-error-suave hover:text-error" aria-label="Eliminar {{ $contacto->nombre_area }}">
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
    @endif

    <livewire:admin.contactos.formulario-contacto />
</div>
