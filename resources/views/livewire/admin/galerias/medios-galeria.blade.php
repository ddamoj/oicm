<div>
    <x-migas
        :items="['Galerías' => route('admin.galerias'), $galeria->titulo => null]"
        raiz="admin.panel"
        etiqueta-raiz="Panel principal"
        class="mb-6"
    />

    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-texto">Medios de «{{ $galeria->titulo }}»</h1>
        <p class="mt-1 text-sm text-texto-secundario">Agrega fotos, videos o enlaces a YouTube/Vimeo. El orden se refleja en el lightbox público.</p>
    </div>

    <x-tarjeta class="mb-8">
        <form wire:submit="agregar" class="space-y-4" x-data="{ tipo: @entangle('tipo') }">
            <div>
                <span class="mb-1.5 block text-sm font-semibold text-texto">Tipo de medio</span>
                <div class="flex gap-4 text-sm text-texto">
                    <label class="flex items-center gap-2">
                        <input type="radio" wire:model.live="tipo" value="foto" class="size-4 border-borde text-primario focus:ring-acento-oscuro">
                        Foto
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" wire:model.live="tipo" value="video" class="size-4 border-borde text-primario focus:ring-acento-oscuro">
                        Video
                    </label>
                </div>
            </div>

            @if ($tipo === 'foto')
                <div>
                    <label for="form-medio-archivo" class="mb-1.5 block text-sm font-semibold text-texto">Archivo de foto (JPG, PNG o WEBP)</label>
                    <input
                        id="form-medio-archivo"
                        type="file"
                        wire:model="archivo"
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto file:mr-4 file:rounded-full file:border-0 file:bg-primario-claro file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primario"
                    >
                    <div wire:loading wire:target="archivo" class="mt-1 text-xs text-texto-secundario">Cargando…</div>
                    @error('archivo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-medio-alt" class="mb-1.5 block text-sm font-semibold text-texto">Texto alternativo</label>
                    <input
                        id="form-medio-alt"
                        type="text"
                        wire:model="descripcionAlt"
                        placeholder="Describe la foto para lectores de pantalla"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('descripcionAlt') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            @else
                <div>
                    <label for="form-medio-video-archivo" class="mb-1.5 block text-sm font-semibold text-texto">Archivo de video (MP4 o WEBM, hasta 80 MB)</label>
                    <input
                        id="form-medio-video-archivo"
                        type="file"
                        wire:model="archivo"
                        accept="video/mp4,video/webm"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto file:mr-4 file:rounded-full file:border-0 file:bg-primario-claro file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primario"
                    >
                    <div wire:loading wire:target="archivo" class="mt-1 text-xs text-texto-secundario">Cargando…</div>
                    @error('archivo') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <p class="text-center text-xs font-semibold uppercase tracking-wide text-texto-secundario">— o —</p>

                <div>
                    <label for="form-medio-url" class="mb-1.5 block text-sm font-semibold text-texto">URL de YouTube o Vimeo</label>
                    <input
                        id="form-medio-url"
                        type="url"
                        wire:model="urlExterna"
                        placeholder="https://www.youtube.com/watch?v=…"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('urlExterna') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="form-medio-video-alt" class="mb-1.5 block text-sm font-semibold text-texto">Descripción (opcional)</label>
                    <input
                        id="form-medio-video-alt"
                        type="text"
                        wire:model="descripcionAlt"
                        class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                    >
                    @error('descripcionAlt') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            @endif

            <div class="flex justify-end pt-2">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="agregar,archivo">
                    <span wire:loading.remove wire:target="agregar">Agregar a la galería</span>
                    <span wire:loading wire:target="agregar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-tarjeta>

    @if ($medios->isEmpty())
        <x-vacio titulo="Aún no hay medios" descripcion="Agrega la primera foto o video con el formulario de arriba." />
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($medios as $indice => $medio)
                <x-tarjeta wire:key="medio-{{ $medio->id }}" class="!p-0 overflow-hidden">
                    <div class="aspect-video w-full bg-gris-claro">
                        @if ($medio->tipo === 'foto')
                            <img src="{{ $medio->urlMiniatura() }}" alt="{{ $medio->descripcion_alt }}" loading="lazy" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-texto-secundario">
                                <svg class="size-10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.5 4.5a3 3 0 00-3 3v9a3 3 0 003 3h8.25a3 3 0 003-3v-9a3 3 0 00-3-3H4.5zM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06z" clip-rule="evenodd" /></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center justify-between gap-2 p-4">
                        <div class="min-w-0">
                            <x-badge :variante="$medio->tipo === 'foto' ? 'primario' : 'acento'">{{ ucfirst($medio->tipo) }}</x-badge>
                            @if ($medio->descripcion_alt)
                                <p class="mt-1.5 truncate text-xs text-texto-secundario">{{ $medio->descripcion_alt }}</p>
                            @endif
                        </div>

                        <div class="flex shrink-0 items-center gap-1">
                            <x-tooltip texto="Mover antes">
                                <button type="button" wire:click="mover({{ $medio->id }}, 'arriba')" @if ($indice === 0) disabled @endif class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario disabled:opacity-30" aria-label="Mover antes">
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" transform="rotate(180 10 10)" /></svg>
                                </button>
                            </x-tooltip>
                            <x-tooltip texto="Mover después">
                                <button type="button" wire:click="mover({{ $medio->id }}, 'abajo')" @if ($indice === $medios->count() - 1) disabled @endif class="rounded-full p-2 text-texto-secundario hover:bg-primario-claro hover:text-primario disabled:opacity-30" aria-label="Mover después">
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-tooltip>
                            <x-tooltip texto="Eliminar">
                                <button
                                    type="button"
                                    x-data
                                    x-on:click="window.alertas.confirmar({ titulo: '¿Eliminar este medio?', texto: 'Esta acción no se puede deshacer.' }).then((confirmado) => { if (confirmado) { $wire.eliminar({{ $medio->id }}); } })"
                                    class="rounded-full p-2 text-texto-secundario hover:bg-error-suave hover:text-error"
                                    aria-label="Eliminar medio"
                                >
                                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-tooltip>
                        </div>
                    </div>
                </x-tarjeta>
            @endforeach
        </div>
    @endif
</div>
