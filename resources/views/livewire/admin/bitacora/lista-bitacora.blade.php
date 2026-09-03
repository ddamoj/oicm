<div>
    <x-migas :items="['Bitácora de auditoría' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-texto">Bitácora de auditoría</h1>
        <p class="mt-1 text-sm text-texto-secundario">Registro de solo lectura de acciones sensibles: quién, qué, cuándo e IP.</p>
    </div>

    <x-tarjeta class="mb-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <select wire:model.live="filtroAccion" class="rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
                <option value="">Todas las acciones</option>
                @foreach ($this->acciones as $accion)
                    <option value="{{ $accion }}">{{ $accion }}</option>
                @endforeach
            </select>

            <select wire:model.live="filtroUsuario" class="rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
                <option value="">Todos los usuarios</option>
                @foreach ($this->usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                @endforeach
            </select>

            <div>
                <label for="bitacora-desde" class="sr-only">Desde</label>
                <input id="bitacora-desde" type="date" wire:model.live="desde" class="w-full rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
            </div>

            <div>
                <label for="bitacora-hasta" class="sr-only">Hasta</label>
                <input id="bitacora-hasta" type="date" wire:model.live="hasta" class="w-full rounded-lg border border-borde bg-superficie px-3 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro">
            </div>
        </div>
    </x-tarjeta>

    @if ($registros->isEmpty())
        <x-vacio titulo="Sin registros que coincidan" descripcion="Ajusta los filtros para ver más resultados." />
    @else
        <x-tarjeta class="!p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Fecha</th>
                            <th class="px-6 py-3 font-semibold">Usuario</th>
                            <th class="px-6 py-3 font-semibold">Acción</th>
                            <th class="px-6 py-3 font-semibold">Detalle</th>
                            <th class="px-6 py-3 font-semibold">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-borde">
                        @foreach ($registros as $registro)
                            <tr wire:key="bitacora-{{ $registro->id }}">
                                <td class="whitespace-nowrap px-6 py-4 text-texto-secundario">{{ $registro->creado_en?->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 font-medium text-texto">{{ $registro->usuario?->name ?? 'Sin sesión (intento anónimo o usuario eliminado)' }}</td>
                                <td class="px-6 py-4"><x-badge variante="primario">{{ $registro->accion }}</x-badge></td>
                                <td class="px-6 py-4 text-texto-secundario">
                                    @if ($registro->modelo_afectado)
                                        {{ $registro->modelo_afectado }} #{{ $registro->modelo_id }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-texto-secundario">{{ $registro->ip ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-tarjeta>

        <x-paginacion :paginador="$registros" />
    @endif
</div>
