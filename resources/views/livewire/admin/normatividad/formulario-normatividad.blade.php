<div>
    <x-modal
        nombre="formulario-normatividad"
        :titulo="$normatividadId ? 'Editar ordenamiento' : 'Nuevo ordenamiento'"
        maxAncho="lg"
    >
        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label for="form-normatividad-titulo" class="mb-1.5 block text-sm font-semibold text-texto">Título</label>
                <input
                    id="form-normatividad-titulo"
                    type="text"
                    wire:model="titulo"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('titulo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-normatividad-ambito" class="mb-1.5 block text-sm font-semibold text-texto">Ámbito</label>
                <select
                    id="form-normatividad-ambito"
                    wire:model="ambito"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                    <option value="federal">Federal</option>
                    <option value="estatal">Estatal</option>
                    <option value="municipal">Municipal</option>
                </select>
                @error('ambito') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-normatividad-medio" class="mb-1.5 block text-sm font-semibold text-texto">Medio de publicación</label>
                <input
                    id="form-normatividad-medio"
                    type="text"
                    wire:model="medioPublicacion"
                    placeholder="Diario Oficial de la Federación, Gaceta Municipal…"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('medioPublicacion') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="form-normatividad-fecha-publicacion" class="mb-1.5 block text-sm font-semibold text-texto">Fecha de publicación</label>
                    <input
                        id="form-normatividad-fecha-publicacion"
                        type="date"
                        wire:model="fechaPublicacion"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('fechaPublicacion') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-normatividad-fecha-reforma" class="mb-1.5 block text-sm font-semibold text-texto">Última reforma</label>
                    <input
                        id="form-normatividad-fecha-reforma"
                        type="date"
                        wire:model="fechaUltimaReforma"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('fechaUltimaReforma') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="form-normatividad-url" class="mb-1.5 block text-sm font-semibold text-texto">
                    Enlace al documento <span class="font-normal text-texto-secundario">(opcional)</span>
                </label>
                <input
                    id="form-normatividad-url"
                    type="url"
                    wire:model="documentoUrl"
                    placeholder="https://…"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('documentoUrl') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-normatividad-descripcion" class="mb-1.5 block text-sm font-semibold text-texto">Descripción</label>
                <textarea
                    id="form-normatividad-descripcion"
                    wire:model="descripcion"
                    rows="3"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                ></textarea>
                @error('descripcion') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-normatividad-orden" class="mb-1.5 block text-sm font-semibold text-texto">Orden</label>
                <input
                    id="form-normatividad-orden"
                    type="number"
                    min="0"
                    wire:model="orden"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('orden') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-texto">
                <input type="checkbox" wire:model="vigente" class="size-4 rounded border-borde text-primario focus:ring-acento-oscuro">
                Visible en la consulta pública
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
