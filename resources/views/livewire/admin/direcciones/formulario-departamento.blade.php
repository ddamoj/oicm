<div>
    <x-modal
        nombre="formulario-departamento"
        :titulo="$departamentoId ? 'Editar departamento' : 'Nuevo departamento'"
        maxAncho="md"
    >
        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label for="form-departamento-nombre" class="mb-1.5 block text-sm font-semibold text-texto">Nombre</label>
                <input
                    id="form-departamento-nombre"
                    type="text"
                    wire:model="nombre"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('nombre') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-departamento-siglas" class="mb-1.5 block text-sm font-semibold text-texto">
                    Siglas <span class="font-normal text-texto-secundario">(opcional)</span>
                </label>
                <input
                    id="form-departamento-siglas"
                    type="text"
                    wire:model="siglas"
                    maxlength="20"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('siglas') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-departamento-descripcion" class="mb-1.5 block text-sm font-semibold text-texto">
                    Descripción <span class="font-normal text-texto-secundario">(opcional)</span>
                </label>
                <textarea
                    id="form-departamento-descripcion"
                    wire:model="descripcion"
                    rows="3"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                ></textarea>
                @error('descripcion') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-departamento-orden" class="mb-1.5 block text-sm font-semibold text-texto">Orden</label>
                <input
                    id="form-departamento-orden"
                    type="number"
                    min="0"
                    wire:model="orden"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                <p class="mt-1 text-xs text-texto-secundario">Los departamentos de menor número se muestran primero dentro de su Dirección.</p>
                @error('orden') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-texto">
                <input type="checkbox" wire:model="activo" class="size-4 rounded border-borde text-primario focus:ring-acento-oscuro">
                Visible en el organigrama público
            </label>

            @error('direccionId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror

            <div class="flex justify-end gap-3 pt-2">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar">
                    <span wire:loading.remove wire:target="guardar">Guardar</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-modal>
</div>
