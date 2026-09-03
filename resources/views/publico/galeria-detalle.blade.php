<x-layouts.publico :titulo="$galeria->titulo">
    <x-hero
        :titulo="$galeria->titulo"
        :subtitulo="$galeria->descripcion ?? ($galeria->fecha_evento?->translatedFormat('d \d\e F \d\e Y'))"
    />

    <x-seccion antetitulo="Galería">
        <x-migas :items="['Galería' => route('galeria'), $galeria->titulo => null]" class="mb-8" />

        @if ($galeria->medios->isEmpty())
            <x-vacio titulo="Esta galería aún no tiene medios" descripcion="En cuanto el OICM agregue fotos o videos, aparecerán aquí." />
        @else
            {{-- Lightbox accesible hecho a mano (sin plugin externo, mismo criterio que el organigrama de Quiénes somos):
                 role="dialog"+aria-modal, cierre con Escape, navegación con flechas y una trampa de foco simple entre
                 los tres botones del diálogo. --}}
            <div
                x-data="{
                    medios: @js($galeria->medios->map->datosLightbox()),
                    abierto: false,
                    indice: 0,
                    abrir(i) { this.indice = i; this.abierto = true; this.$nextTick(() => this.$refs.botonCerrar?.focus()); },
                    cerrar() { this.abierto = false; this.$nextTick(() => this.$refs['miniatura-' + this.indice]?.focus()); },
                    anterior() { this.indice = (this.indice - 1 + this.medios.length) % this.medios.length; },
                    siguiente() { this.indice = (this.indice + 1) % this.medios.length; },
                }"
                x-on:keydown.escape.window="if (abierto) cerrar()"
                x-on:keydown.arrow-left.window="if (abierto) anterior()"
                x-on:keydown.arrow-right.window="if (abierto) siguiente()"
            >
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($galeria->medios as $indice => $medio)
                        <button
                            type="button"
                            x-ref="miniatura-{{ $indice }}"
                            x-on:click="abrir({{ $indice }})"
                            class="group relative aspect-video overflow-hidden rounded-xl bg-gris-claro focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                            aria-label="Ver {{ $medio->tipo === 'foto' ? 'foto' : 'video' }} {{ $indice + 1 }} de {{ $galeria->medios->count() }}{{ $medio->descripcion_alt ? ': '.$medio->descripcion_alt : '' }}"
                        >
                            @if ($medio->urlMiniatura())
                                <img src="{{ $medio->urlMiniatura() }}" alt="" loading="lazy" class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-texto-secundario">
                                    <svg class="size-10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.5 4.5a3 3 0 00-3 3v9a3 3 0 003 3h8.25a3 3 0 003-3v-9a3 3 0 00-3-3H4.5zM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06z" clip-rule="evenodd" /></svg>
                                </div>
                            @endif

                            @if ($medio->tipo === 'video')
                                <span class="absolute inset-0 flex items-center justify-center bg-texto/20">
                                    <span class="flex size-12 items-center justify-center rounded-full bg-white/90 text-primario">
                                        <svg class="size-5 translate-x-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" /></svg>
                                    </span>
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>

                {{-- Diálogo modal --}}
                <div
                    x-show="abierto"
                    x-cloak
                    x-on:click.self="cerrar()"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-texto/90 p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Visor de medios de la galería"
                >
                    <template x-if="abierto">
                        <div class="relative w-full max-w-4xl">
                            <div class="flex items-center justify-between pb-3">
                                <p class="text-sm font-medium text-white/80" x-text="(indice + 1) + ' / ' + medios.length"></p>
                                <button type="button" x-ref="botonCerrar" x-on:click="cerrar()" x-on:keydown.tab.prevent="$refs.botonAnterior.focus()" class="rounded-full p-2 text-white hover:bg-white/10" aria-label="Cerrar visor">
                                    <svg class="size-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                                </button>
                            </div>

                            <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-texto">
                                <template x-if="medios[indice].tipo === 'foto'">
                                    <img :src="medios[indice].urlGrande" :alt="medios[indice].alt" class="h-full w-full object-contain">
                                </template>

                                <template x-if="medios[indice].tipo === 'video' && medios[indice].urlGrande">
                                    <video :src="medios[indice].urlGrande" controls class="h-full w-full"></video>
                                </template>

                                <template x-if="medios[indice].tipo === 'video' && !medios[indice].urlGrande && medios[indice].proveedorExterno">
                                    <iframe
                                        :src="medios[indice].proveedorExterno.proveedor === 'youtube'
                                            ? 'https://www.youtube-nocookie.com/embed/' + medios[indice].proveedorExterno.id
                                            : 'https://player.vimeo.com/video/' + medios[indice].proveedorExterno.id"
                                        class="h-full w-full"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        :title="medios[indice].alt || 'Video de la galería'"
                                    ></iframe>
                                </template>
                            </div>

                            <p class="mt-3 text-center text-sm text-white/70" x-show="medios[indice].alt" x-text="medios[indice].alt"></p>

                            <button type="button" x-ref="botonAnterior" x-on:click="anterior()" x-on:keydown.tab.shift.prevent="$refs.botonSiguiente.focus()" class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-2.5 text-white hover:bg-white/20" aria-label="Medio anterior">
                                <svg class="size-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                            </button>

                            <button type="button" x-ref="botonSiguiente" x-on:click="siguiente()" x-on:keydown.tab.prevent="$refs.botonCerrar.focus()" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-2.5 text-white hover:bg-white/20" aria-label="Medio siguiente">
                                <svg class="size-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        @endif
    </x-seccion>
</x-layouts.publico>
