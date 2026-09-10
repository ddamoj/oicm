<div>
    <x-modal
        nombre="formulario-direccion"
        :titulo="$direccionId ? 'Editar Dirección' : 'Nueva Dirección'"
        maxAncho="md"
    >
        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label for="form-direccion-nombre" class="mb-1.5 block text-sm font-semibold text-texto">Nombre</label>
                <input
                    id="form-direccion-nombre"
                    type="text"
                    wire:model="nombre"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('nombre') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            {{-- La clave es la URL pública de la Dirección: se muestra en
                 edición solo como referencia y nunca se recalcula, para no
                 romper enlaces ya difundidos. --}}
            @if ($direccionId)
                <div>
                    <span class="mb-1.5 block text-sm font-semibold text-texto">Dirección pública</span>
                    <p class="rounded-lg border border-borde bg-gris-claro px-4 py-2.5 text-sm text-texto-secundario">
                        /direcciones/{{ $clave }}
                    </p>
                    <p class="mt-1 text-xs text-texto-secundario">No cambia al renombrar la Dirección: los enlaces ya publicados siguen funcionando.</p>
                </div>
            @endif

            <div>
                <label for="form-direccion-siglas" class="mb-1.5 block text-sm font-semibold text-texto">
                    Siglas <span class="font-normal text-texto-secundario">(opcional)</span>
                </label>
                <input
                    id="form-direccion-siglas"
                    type="text"
                    wire:model="siglas"
                    maxlength="20"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('siglas') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-direccion-descripcion" class="mb-1.5 block text-sm font-semibold text-texto">
                    Descripción <span class="font-normal text-texto-secundario">(opcional)</span>
                </label>
                <textarea
                    id="form-direccion-descripcion"
                    wire:model="descripcion"
                    rows="3"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                ></textarea>
                <p class="mt-1 text-xs text-texto-secundario">Se muestra bajo el nombre en el organigrama y como subtítulo de la página pública.</p>
                @error('descripcion') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-direccion-orden" class="mb-1.5 block text-sm font-semibold text-texto">Orden</label>
                <input
                    id="form-direccion-orden"
                    type="number"
                    min="0"
                    wire:model="orden"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                <p class="mt-1 text-xs text-texto-secundario">Las Direcciones de menor número se muestran primero en el organigrama.</p>
                @error('orden') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-texto">
                <input type="checkbox" wire:model="activa" class="size-4 rounded border-borde text-primario focus:ring-acento-oscuro">
                Visible en el organigrama público
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar">
                    <span wire:loading.remove wire:target="guardar">Guardar</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-modal>
</div>
