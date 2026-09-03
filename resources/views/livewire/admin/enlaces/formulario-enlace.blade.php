<div>
    <x-modal
        nombre="formulario-enlace"
        :titulo="$enlaceId ? 'Editar enlace' : 'Nuevo enlace'"
        maxAncho="md"
    >
        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label for="form-enlace-nombre" class="mb-1.5 block text-sm font-semibold text-texto">Nombre</label>
                <input
                    id="form-enlace-nombre"
                    type="text"
                    wire:model="nombre"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('nombre') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-enlace-url" class="mb-1.5 block text-sm font-semibold text-texto">URL</label>
                <input
                    id="form-enlace-url"
                    type="url"
                    wire:model="url"
                    placeholder="https://…"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('url') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-enlace-categoria" class="mb-1.5 block text-sm font-semibold text-texto">Categoría</label>
                <select
                    id="form-enlace-categoria"
                    wire:model="categoriaEnlaceId"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                    <option value="">Selecciona una categoría…</option>
                    @foreach ($this->categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
                @error('categoriaEnlaceId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-enlace-descripcion" class="mb-1.5 block text-sm font-semibold text-texto">Descripción</label>
                <textarea
                    id="form-enlace-descripcion"
                    wire:model="descripcion"
                    rows="3"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                ></textarea>
                @error('descripcion') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-enlace-orden" class="mb-1.5 block text-sm font-semibold text-texto">Orden</label>
                <input
                    id="form-enlace-orden"
                    type="number"
                    min="0"
                    wire:model="orden"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                <p class="mt-1 text-xs text-texto-secundario">Los enlaces de menor número se muestran primero dentro de su categoría.</p>
                @error('orden') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-texto">
                <input type="checkbox" wire:model="activo" class="size-4 rounded border-borde text-primario focus:ring-acento-oscuro">
                Visible en el directorio público
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
