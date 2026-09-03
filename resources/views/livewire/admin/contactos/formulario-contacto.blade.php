<div>
    <x-modal
        nombre="formulario-contacto"
        :titulo="$contactoId ? 'Editar contacto' : 'Nuevo contacto'"
        maxAncho="lg"
    >
        <form wire:submit="guardar" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="form-contacto-area" class="mb-1.5 block text-sm font-semibold text-texto">Nombre del área</label>
                    <input
                        id="form-contacto-area"
                        type="text"
                        wire:model="nombreArea"
                        required
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('nombreArea') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-contacto-direccion" class="mb-1.5 block text-sm font-semibold text-texto">Dirección asociada</label>
                    <select
                        id="form-contacto-direccion"
                        wire:model="direccionId"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                        <option value="">General (sin Dirección)</option>
                        @foreach ($this->direcciones as $direccion)
                            <option value="{{ $direccion->id }}">{{ $direccion->nombre }}</option>
                        @endforeach
                    </select>
                    @error('direccionId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="form-contacto-domicilio" class="mb-1.5 block text-sm font-semibold text-texto">Domicilio</label>
                <input
                    id="form-contacto-domicilio"
                    type="text"
                    wire:model="domicilio"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('domicilio') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="form-contacto-telefono" class="mb-1.5 block text-sm font-semibold text-texto">Teléfono</label>
                    <input
                        id="form-contacto-telefono"
                        type="text"
                        wire:model="telefono"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('telefono') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-contacto-correo" class="mb-1.5 block text-sm font-semibold text-texto">Correo electrónico</label>
                    <input
                        id="form-contacto-correo"
                        type="email"
                        wire:model="correo"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('correo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="form-contacto-horario" class="mb-1.5 block text-sm font-semibold text-texto">Horario de atención</label>
                <input
                    id="form-contacto-horario"
                    type="text"
                    wire:model="horario"
                    placeholder="Lunes a viernes, 9:00 a 17:00 h"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('horario') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="form-contacto-latitud" class="mb-1.5 block text-sm font-semibold text-texto">Latitud</label>
                    <input
                        id="form-contacto-latitud"
                        type="text"
                        inputmode="decimal"
                        wire:model="latitud"
                        placeholder="17.0654"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('latitud') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-contacto-longitud" class="mb-1.5 block text-sm font-semibold text-texto">Longitud</label>
                    <input
                        id="form-contacto-longitud"
                        type="text"
                        inputmode="decimal"
                        wire:model="longitud"
                        placeholder="-96.7236"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('longitud') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="-mt-2 text-xs text-texto-secundario">Ambas son opcionales; si se capturan, se muestra el mapa embebido en la página pública de Contacto.</p>

            <div>
                <label for="form-contacto-quejas" class="mb-1.5 block text-sm font-semibold text-texto">Canal de quejas y denuncias</label>
                <input
                    id="form-contacto-quejas"
                    type="text"
                    wire:model="canalQuejasDenuncias"
                    placeholder="Correo, teléfono o enlace del buzón de quejas de la DQDISP"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('canalQuejasDenuncias') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-contacto-orden" class="mb-1.5 block text-sm font-semibold text-texto">Orden</label>
                <input
                    id="form-contacto-orden"
                    type="number"
                    min="0"
                    wire:model="orden"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('orden') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar">
                    <span wire:loading.remove wire:target="guardar">Guardar</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-modal>
</div>
