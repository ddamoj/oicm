<div>
    <x-modal
        nombre="formulario-pagina"
        :titulo="$paginaId ? 'Editar página institucional' : 'Nueva página institucional'"
        maxAncho="xl"
    >
        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label for="form-pagina-titulo" class="mb-1.5 block text-sm font-semibold text-texto">Título</label>
                <input
                    id="form-pagina-titulo"
                    type="text"
                    wire:model="titulo"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('titulo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-texto">Contenido</label>

                {{-- wire:ignore: Livewire no debe volver a renderizar el editor en cada
                     tecleo; Quill sincroniza su HTML hacia "contenido" por su cuenta. --}}
                <div
                    wire:ignore
                    x-data="editorEnriquecido(@js($contenido), 'contenido')"
                    x-init="iniciar()"
                    class="rounded-lg border border-borde bg-superficie"
                >
                    <div x-ref="editor" class="min-h-[320px]"></div>
                </div>
                @error('contenido') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="form-pagina-direccion" class="mb-1.5 block text-sm font-semibold text-texto">
                        Dirección <span class="font-normal text-texto-secundario">(opcional)</span>
                    </label>
                    <select
                        id="form-pagina-direccion"
                        wire:model="direccionId"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                        <option value="">Página general (sin dirección)</option>
                        @foreach ($this->direcciones as $direccion)
                            <option value="{{ $direccion->id }}">{{ $direccion->nombre }}</option>
                        @endforeach
                    </select>
                    @error('direccionId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-pagina-estatus" class="mb-1.5 block text-sm font-semibold text-texto">Estatus</label>
                    <select
                        id="form-pagina-estatus"
                        wire:model="estatus"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                        <option value="borrador">Borrador</option>
                        <option value="publicada">Publicada</option>
                    </select>
                    @error('estatus') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
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
