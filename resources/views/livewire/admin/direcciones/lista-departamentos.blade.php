<div>
    <x-migas
        :items="['Direcciones' => route('admin.direcciones'), $direccion->nombre => null]"
        raiz="admin.panel"
        etiqueta-raiz="Panel principal"
        class="mb-6"
    />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Departamentos</h1>
            <p class="mt-1 text-sm text-texto-secundario">
                Departamentos que integran {{ $direccion->nombre }} dentro del organigrama público.
            </p>
        </div>

        <x-boton wire:click="nuevo" variante="primario">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Nuevo departamento
        </x-boton>
    </div>

    {{-- Los nombres sembrados en la Fase 7 son provisionales: el organigrama
         del documento de carga es una imagen sin texto extraíble. --}}
    <x-tarjeta class="mb-6 border-advertencia/30 bg-advertencia-suave/40">
        <p class="text-sm text-texto">
            Los nombres de los departamentos están pendientes de confirmación oficial por el OICM.
            Edítalos aquí en cuanto se confirmen: el organigrama público se actualiza de inmediato.
        </p>
    </x-tarjeta>

    @if ($departamentos->isEmpty())
        <x-vacio
            titulo="Sin departamentos"
            descripcion="Esta Dirección todavía no tiene departamentos registrados en el organigrama."
        />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Departamento</th>
                            <th class="px-6 py-3 font-semibold">Orden</th>
                            <th class="px-6 py-3 font-semibold">Estado</th>
                            <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($departamentos as $departamento)
                            <tr wire:key="departamento-{{ $departamento->id }}">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-texto">{{ $departamento->nombre }}</div>
                                    @if ($departamento->siglas)
                                        <div class="text-xs font-semibold uppercase tracking-wide text-texto-secundario">{{ $departamento->siglas }}</div>
                                    @endif
                                    @if ($departamento->descripcion)
                                        <div class="text-xs text-texto-secundario">{{ Str::limit($departamento->descripcion, 70) }}</div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-texto-secundario">{{ $departamento->orden }}</td>

                                <td class="px-6 py-4">
                                    @if ($departamento->activo)
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
                                                if (confirmado) { $wire[metodo]({{ $departamento->id }}); }
                                            },
                                        }"
                                    >
                                        <x-tooltip texto="Editar">
                                            <button type="button" wire:click="editar({{ $departamento->id }})" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Editar {{ $departamento->nombre }}">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                            </button>
                                        </x-tooltip>

                                        <x-tooltip texto="{{ $departamento->activo ? 'Retirar del organigrama' : 'Mostrar en el organigrama' }}">
                                            <button type="button" x-on:click="confirmarYEjecutar('alternarActivo', '{{ $departamento->activo ? '¿Retirar' : '¿Mostrar' }} este departamento?', 'El cambio se refleja de inmediato en el organigrama público.')" class="rounded-full p-2 text-texto-secundario hover:bg-advertencia-suave hover:text-advertencia" aria-label="{{ $departamento->activo ? 'Retirar' : 'Mostrar' }} {{ $departamento->nombre }}">
                                                @if ($departamento->activo)
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd" /><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z" /></svg>
                                                @else
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" /><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                                @endif
                                            </button>
                                        </x-tooltip>

                                        <x-tooltip texto="Eliminar">
                                            <button type="button" x-on:click="confirmarYEjecutar('eliminar', '¿Eliminar {{ $departamento->nombre }}?', 'Esta acción no se puede deshacer.')" class="rounded-full p-2 text-texto-secundario hover:bg-error-suave hover:text-error" aria-label="Eliminar {{ $departamento->nombre }}">
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

    <livewire:admin.direcciones.formulario-departamento />
</div>
