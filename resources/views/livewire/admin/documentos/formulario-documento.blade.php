<div>
    <x-modal
        nombre="formulario-documento"
        :titulo="$modoReemplazo ? 'Reemplazar archivo' : ($documentoId ? 'Editar documento' : 'Cargar documento')"
        maxAncho="md"
    >
        <form wire:submit="guardar" class="space-y-4">
            @unless ($modoReemplazo)
                <div>
                    <label for="form-documento-nombre" class="mb-1.5 block text-sm font-semibold text-texto">Nombre</label>
                    <input
                        id="form-documento-nombre"
                        type="text"
                        wire:model="nombre"
                        required
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('nombre') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-documento-descripcion" class="mb-1.5 block text-sm font-semibold text-texto">Descripción</label>
                    <textarea
                        id="form-documento-descripcion"
                        wire:model="descripcion"
                        rows="3"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    ></textarea>
                    @error('descripcion') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-documento-categoria" class="mb-1.5 block text-sm font-semibold text-texto">Categoría</label>
                    <select
                        id="form-documento-categoria"
                        wire:model="categoriaDocumentoId"
                        required
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                        <option value="">Selecciona una categoría…</option>
                        @foreach ($this->categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoriaDocumentoId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-documento-direccion" class="mb-1.5 block text-sm font-semibold text-texto">Dirección responsable</label>
                    <select
                        id="form-documento-direccion"
                        wire:model="direccionId"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                        <option value="">Sin dirección específica</option>
                        @foreach ($this->direcciones as $direccion)
                            <option value="{{ $direccion->id }}">{{ $direccion->nombre }}</option>
                        @endforeach
                    </select>
                    @error('direccionId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-texto">
                    <input type="checkbox" wire:model="publicado" class="size-4 rounded border-borde text-primario focus:ring-acento-oscuro">
                    Visible en la vista pública
                </label>
            @endunless

            <div>
                <label for="form-documento-archivo" class="mb-1.5 block text-sm font-semibold text-texto">
                    Archivo
                    @if ($documentoId && ! $modoReemplazo)
                        <span class="font-normal text-texto-secundario">(déjalo vacío para conservar el actual)</span>
                    @endif
                </label>
                <input
                    id="form-documento-archivo"
                    type="file"
                    wire:model="archivo"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.csv"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto file:mr-3 file:rounded-full file:border-0 file:bg-primario-claro file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primario focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                <p class="mt-1 text-xs text-texto-secundario">PDF, DOC, DOCX, XLS, XLSX o CSV. Máximo 25 MB.</p>
                <div wire:loading wire:target="archivo" class="mt-1 text-xs text-texto-secundario">Subiendo archivo…</div>
                @error('archivo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar,archivo">
                    <span wire:loading.remove wire:target="guardar">Guardar</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-modal>
</div>
