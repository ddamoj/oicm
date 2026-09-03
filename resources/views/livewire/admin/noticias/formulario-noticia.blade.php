<div>
    <x-modal
        nombre="formulario-noticia"
        :titulo="$noticiaId ? 'Editar noticia' : 'Nueva noticia'"
        maxAncho="xl"
    >
        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label for="form-noticia-titulo" class="mb-1.5 block text-sm font-semibold text-texto">Título</label>
                <input
                    id="form-noticia-titulo"
                    type="text"
                    wire:model="titulo"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('titulo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-noticia-resumen" class="mb-1.5 block text-sm font-semibold text-texto">
                    Resumen <span class="font-normal text-texto-secundario">(opcional, para la tarjeta del listado)</span>
                </label>
                <textarea
                    id="form-noticia-resumen"
                    wire:model="resumen"
                    rows="2"
                    maxlength="300"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                ></textarea>
                @error('resumen') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
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
                    <div x-ref="editor" class="min-h-[220px]"></div>
                </div>
                @error('contenido') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="form-noticia-estatus" class="mb-1.5 block text-sm font-semibold text-texto">Estatus</label>
                    <select
                        id="form-noticia-estatus"
                        wire:model.live="estatus"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                        <option value="borrador">Borrador</option>
                        <option value="publicada">Publicada</option>
                    </select>
                    @error('estatus') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                @if ($estatus === 'publicada')
                    <div>
                        <label for="form-noticia-fecha" class="mb-1.5 block text-sm font-semibold text-texto">
                            Fecha de publicación
                            <span class="font-normal text-texto-secundario">(futura = programada)</span>
                        </label>
                        <input
                            id="form-noticia-fecha"
                            type="datetime-local"
                            wire:model="publicadoEn"
                            class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                        >
                        @error('publicadoEn') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>

            <div>
                <label for="form-noticia-imagen" class="mb-1.5 block text-sm font-semibold text-texto">
                    Imagen de portada
                    @if ($noticiaId)
                        <span class="font-normal text-texto-secundario">(déjala vacía para conservar la actual)</span>
                    @endif
                </label>

                @if ($imagenActualUrl && ! $imagen)
                    <img src="{{ $imagenActualUrl }}" alt="" class="mb-2 h-24 w-auto rounded-lg border border-borde object-cover">
                @endif

                <input
                    id="form-noticia-imagen"
                    type="file"
                    wire:model="imagen"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto file:mr-3 file:rounded-full file:border-0 file:bg-primario-claro file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primario focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                <p class="mt-1 text-xs text-texto-secundario">JPG, PNG o WEBP. Máximo 4 MB, mínimo 400x300 px.</p>
                <div wire:loading wire:target="imagen" class="mt-1 text-xs text-texto-secundario">Subiendo imagen…</div>
                @error('imagen') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            @if ($imagen || $imagenActualUrl)
                <div>
                    <label for="form-noticia-imagen-alt" class="mb-1.5 block text-sm font-semibold text-texto">
                        Texto alternativo de la imagen
                        <span class="font-normal text-texto-secundario">(accesibilidad, obligatorio)</span>
                    </label>
                    <input
                        id="form-noticia-imagen-alt"
                        type="text"
                        wire:model="imagenAlt"
                        maxlength="250"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('imagenAlt') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="flex justify-end gap-3 pt-2">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar,imagen">
                    <span wire:loading.remove wire:target="guardar">Guardar</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-modal>
</div>
