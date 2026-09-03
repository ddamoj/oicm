<div>
    <x-modal
        nombre="formulario-galeria"
        :titulo="$galeriaId ? 'Editar galería' : 'Nueva galería'"
        maxAncho="md"
    >
        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label for="form-galeria-titulo" class="mb-1.5 block text-sm font-semibold text-texto">Título</label>
                <input
                    id="form-galeria-titulo"
                    type="text"
                    wire:model="titulo"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('titulo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-galeria-descripcion" class="mb-1.5 block text-sm font-semibold text-texto">Descripción</label>
                <textarea
                    id="form-galeria-descripcion"
                    wire:model="descripcion"
                    rows="3"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                ></textarea>
                @error('descripcion') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-galeria-fecha" class="mb-1.5 block text-sm font-semibold text-texto">Fecha del evento</label>
                <input
                    id="form-galeria-fecha"
                    type="date"
                    wire:model="fechaEvento"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('fechaEvento') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-texto">
                <input type="checkbox" wire:model="publicada" class="size-4 rounded border-borde text-primario focus:ring-acento-oscuro">
                Visible en el grid público
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
