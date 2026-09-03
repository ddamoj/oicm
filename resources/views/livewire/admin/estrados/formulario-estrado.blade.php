<div>
    <x-modal nombre="formulario-estrado" titulo="Publicar estrado digital" maxAncho="md">
        <form wire:submit="guardar" class="space-y-4">
            <p class="rounded-lg bg-primario-claro px-4 py-3 text-xs leading-relaxed text-primario-oscuro">
                El número de folio lo asigna el sistema al publicar y no puede cambiar. Una vez publicado, el archivo no se reemplaza: si necesitas corregirlo, retíralo y publica uno nuevo.
            </p>

            <div>
                <label for="form-estrado-asunto" class="mb-1.5 block text-sm font-semibold text-texto">Asunto</label>
                <input
                    id="form-estrado-asunto"
                    type="text"
                    wire:model="asunto"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('asunto') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-estrado-expediente" class="mb-1.5 block text-sm font-semibold text-texto">
                    Expediente <span class="font-normal text-texto-secundario">(opcional)</span>
                </label>
                <input
                    id="form-estrado-expediente"
                    type="text"
                    wire:model="expediente"
                    placeholder="OICM/DRACS/000/2026"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('expediente') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-estrado-archivo" class="mb-1.5 block text-sm font-semibold text-texto">Archivo (PDF)</label>
                <input
                    id="form-estrado-archivo"
                    type="file"
                    wire:model="archivo"
                    accept=".pdf"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto file:mr-3 file:rounded-full file:border-0 file:bg-primario-claro file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primario focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                <p class="mt-1 text-xs text-texto-secundario">Solo PDF. Máximo 25 MB.</p>
                <div wire:loading wire:target="archivo" class="mt-1 text-xs text-texto-secundario">Subiendo archivo…</div>
                @error('archivo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-start gap-2 text-sm text-texto">
                <input type="checkbox" wire:model="datosTestados" class="mt-0.5 size-4 rounded border-borde text-primario focus:ring-acento-oscuro">
                <span>Confirmo que el documento es una versión pública: los datos personales que contiene ya fueron testados conforme al procedimiento correspondiente.</span>
            </label>
            @error('datosTestados') <p class="text-xs text-error">{{ $message }}</p> @enderror

            <div class="flex justify-end gap-3 pt-2">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar,archivo">
                    <span wire:loading.remove wire:target="guardar">Publicar</span>
                    <span wire:loading wire:target="guardar">Publicando…</span>
                </x-boton>
            </div>
        </form>
    </x-modal>
</div>
