<div>
    <x-migas :items="['Usuarios' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Usuarios</h1>
            <p class="mt-1 text-sm text-texto-secundario">Alta, edición y baja de cuentas administrativas.</p>
        </div>

        <x-boton wire:click="nuevo" variante="primario">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Nuevo usuario
        </x-boton>
    </div>

    <x-tarjeta class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <label for="busqueda-usuarios" class="sr-only">Buscar usuarios</label>
                <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input
                    type="search"
                    id="busqueda-usuarios"
                    wire:model.live.debounce.400ms="busqueda"
                    placeholder="Buscar por nombre o correo…"
                    class="w-full rounded-full border border-borde bg-superficie py-3 pl-11 pr-4 text-sm text-texto
                           placeholder:text-gris focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
            </div>

            <select wire:model.live="filtroRol" class="rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
                <option value="">Todos los roles</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                @endforeach
            </select>

            <select wire:model.live="filtroEstado" class="rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="inactivo">Desactivados</option>
            </select>
        </div>
    </x-tarjeta>

    @if ($usuarios->isEmpty())
        <x-vacio titulo="Sin usuarios que coincidan" descripcion="Ajusta la búsqueda o los filtros para ver más resultados." />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Nombre</th>
                            <th class="px-6 py-3 font-semibold">Correo</th>
                            <th class="px-6 py-3 font-semibold">Rol</th>
                            <th class="px-6 py-3 font-semibold">Estado</th>
                            <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($usuarios as $usuario)
                            <tr wire:key="usuario-{{ $usuario->id }}">
                                <td class="px-6 py-4 font-medium text-texto">{{ $usuario->name }}</td>
                                <td class="px-6 py-4 text-texto-secundario">{{ $usuario->email }}</td>
                                <td class="px-6 py-4">
                                    <x-badge variante="primario">{{ $usuario->rol?->nombre ?? 'Sin rol' }}</x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($usuario->activo)
                                        <x-badge variante="exito" :punto="true">Activo</x-badge>
                                    @else
                                        <x-badge variante="error" :punto="true">Desactivado</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                        x-data="{
                                            async confirmarYEjecutar(metodo, titulo, texto) {
                                                const confirmado = await window.alertas.confirmar({ titulo, texto });
                                                if (confirmado) { $wire[metodo]({{ $usuario->id }}); }
                                            },
                                        }"
                                    >
                                        <button type="button" wire:click="editar({{ $usuario->id }})" class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Editar a {{ $usuario->name }}">
                                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                        </button>

                                        @if ($usuario->id !== auth()->id())
                                            @if ($usuario->activo)
                                                <button type="button" x-on:click="confirmarYEjecutar('desactivar', '¿Desactivar a {{ $usuario->name }}?', 'Su acceso se revocará de inmediato. Podrás reactivarlo después.')" class="rounded-full p-2 text-texto-secundario hover:bg-advertencia-suave hover:text-advertencia" aria-label="Desactivar a {{ $usuario->name }}">
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                                                </button>
                                            @else
                                                <button type="button" x-on:click="confirmarYEjecutar('activar', '¿Reactivar a {{ $usuario->name }}?', 'Recuperará acceso al panel de administración.')" class="rounded-full p-2 text-texto-secundario hover:bg-exito-suave hover:text-exito" aria-label="Reactivar a {{ $usuario->name }}">
                                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.03-9.97a.75.75 0 00-1.06-1.06L9 9.94 7.03 7.97a.75.75 0 00-1.06 1.06l2.5 2.5a.75.75 0 001.06 0l4-4z" clip-rule="evenodd" /></svg>
                                                </button>
                                            @endif

                                            <button type="button" x-on:click="confirmarYEjecutar('eliminar', '¿Eliminar a {{ $usuario->name }}?', 'Esta acción no se puede deshacer desde el panel.')" class="rounded-full p-2 text-texto-secundario hover:bg-error-suave hover:text-error" aria-label="Eliminar a {{ $usuario->name }}">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-tarjeta>

        <x-paginacion :paginador="$usuarios" />
    @endif

    <livewire:admin.usuarios.formulario-usuario />
</div>
