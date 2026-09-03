<x-layouts.publico :titulo="$noticia->titulo" :descripcion="$noticia->resumen">
    <article>
        @if ($noticia->urlImagen())
            <div class="aspect-[21/9] w-full overflow-hidden bg-gris-claro">
                <img
                    src="{{ $noticia->urlImagen() }}"
                    alt="{{ $noticia->imagen_alt }}"
                    class="h-full w-full object-cover"
                >
            </div>
        @endif

        <x-seccion ancho="max-w-3xl">
            <x-migas :items="['Noticias' => route('noticias'), $noticia->titulo => null]" class="mb-8" />

            <p class="text-sm font-semibold uppercase tracking-wide text-texto-secundario">
                {{ $noticia->publicado_en->translatedFormat('d \d\e F \d\e Y, h:i A') }}
            </p>

            <h1 class="mt-3 text-4xl font-bold leading-[1.1] tracking-tight text-texto sm:text-5xl">
                {{ $noticia->titulo }}
            </h1>

            <div class="prose prose-lg mt-8 max-w-none text-texto prose-headings:text-texto prose-a:text-primario">
                {!! $noticia->contenido !!}
            </div>

            {{-- Compartir: enlaces estándar a cada red y copiar al portapapeles vía SweetAlert2. --}}
            <div class="mt-12 flex flex-wrap items-center gap-3 border-t border-borde pt-6" x-data="{
                url: window.location.href,
                copiar() {
                    navigator.clipboard.writeText(this.url)
                        .then(() => window.alertas.toast('Enlace copiado.'))
                        .catch(() => window.alertas.error('No fue posible copiar el enlace.'));
                },
            }">
                <span class="text-sm font-semibold text-texto-secundario">Compartir:</span>

                <a
                    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('noticias.mostrar', $noticia)) }}"
                    target="_blank" rel="noopener noreferrer"
                    class="rounded-full bg-gris-claro p-2.5 text-texto-secundario hover:bg-primario-claro hover:text-primario"
                    aria-label="Compartir en Facebook"
                >
                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12.06C22 6.505 17.523 2 12 2S2 6.505 2 12.06c0 5.02 3.657 9.184 8.438 9.94v-7.03H7.898v-2.91h2.54V9.845c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562v1.875h2.773l-.443 2.91h-2.33V22c4.78-.756 8.437-4.92 8.437-9.94z" /></svg>
                </a>

                <a
                    href="https://twitter.com/intent/tweet?url={{ urlencode(route('noticias.mostrar', $noticia)) }}&text={{ urlencode($noticia->titulo) }}"
                    target="_blank" rel="noopener noreferrer"
                    class="rounded-full bg-gris-claro p-2.5 text-texto-secundario hover:bg-primario-claro hover:text-primario"
                    aria-label="Compartir en X"
                >
                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
                </a>

                <a
                    href="https://wa.me/?text={{ urlencode($noticia->titulo.' '.route('noticias.mostrar', $noticia)) }}"
                    target="_blank" rel="noopener noreferrer"
                    class="rounded-full bg-gris-claro p-2.5 text-texto-secundario hover:bg-primario-claro hover:text-primario"
                    aria-label="Compartir en WhatsApp"
                >
                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2.05 22l5.25-1.38a9.87 9.87 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2zm0 18.13a8.2 8.2 0 01-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 01-1.26-4.36c0-4.55 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.18 8.18 0 012.41 5.82c0 4.55-3.7 8.24-8.25 8.24z" /></svg>
                </a>

                <button type="button" x-on:click="copiar()" class="rounded-full bg-gris-claro p-2.5 text-texto-secundario hover:bg-primario-claro hover:text-primario" aria-label="Copiar enlace">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M12.232 4.232a2.5 2.5 0 013.536 3.536l-1.225 1.224a.75.75 0 001.061 1.06l1.224-1.224a4 4 0 00-5.656-5.656l-3 3a4 4 0 00.225 5.865.75.75 0 00.977-1.138 2.5 2.5 0 01-.142-3.667l3-3z" /><path d="M11.603 7.963a.75.75 0 00-.977 1.138 2.5 2.5 0 01.142 3.667l-3 3a2.5 2.5 0 01-3.536-3.536l1.225-1.224a.75.75 0 00-1.061-1.06l-1.224 1.224a4 4 0 105.656 5.656l3-3a4 4 0 00-.225-5.865z" /></svg>
                </button>
            </div>
        </x-seccion>

        @if ($otras->isNotEmpty())
            <x-seccion alterna titulo="Otras noticias" ancho="max-w-5xl">
                <div class="grid gap-6 sm:grid-cols-3">
                    @foreach ($otras as $relacionada)
                        <x-tarjeta wire:key="relacionada-{{ $relacionada->id }}" href="{{ route('noticias.mostrar', $relacionada) }}" flotante>
                            <p class="text-xs font-semibold uppercase tracking-wide text-texto-secundario">
                                {{ $relacionada->publicado_en?->translatedFormat('d \d\e F \d\e Y') }}
                            </p>
                            <h3 class="mt-2 text-base font-bold tracking-tight text-texto">{{ $relacionada->titulo }}</h3>
                        </x-tarjeta>
                    @endforeach
                </div>
            </x-seccion>
        @endif
    </article>
</x-layouts.publico>
