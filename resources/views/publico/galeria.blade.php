<x-layouts.publico titulo="Galería">
    <x-hero
        titulo="Galería de fotos y videos"
        subtitulo="Capacitaciones, sesiones del COCODI, Comité de Obras y procesos de entrega-recepción del OICM."
    />

    <x-seccion antetitulo="Memoria institucional">
        <x-migas :items="['Galería' => null]" class="mb-8" />

        @if ($galerias->isEmpty())
            <x-vacio
                titulo="Aún no hay galerías publicadas"
                descripcion="En cuanto el OICM publique fotos o videos de un evento, aparecerán aquí de forma automática."
            />
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($galerias as $galeria)
                    <x-tarjeta wire:key="galeria-{{ $galeria->id }}" href="{{ route('galeria.mostrar', $galeria) }}" :flotante="true" class="!p-0 overflow-hidden">
                        <div class="aspect-[3/2] w-full overflow-hidden bg-gris-claro">
                            @php $portada = $galeria->medios->first(); @endphp
                            @if ($portada?->urlMiniatura())
                                <img src="{{ $portada->urlMiniatura() }}" alt="" loading="lazy" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-gris">
                                    <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-semibold uppercase tracking-wide text-texto-secundario">
                                {{ $galeria->fecha_evento?->translatedFormat('d \d\e F \d\e Y') ?? 'Sin fecha' }}
                                · {{ $galeria->medios_count }} {{ Str::plural('archivo', $galeria->medios_count) }}
                            </p>
                            <h3 class="mt-2 text-lg font-bold tracking-tight text-texto">{{ $galeria->titulo }}</h3>
                            @if ($galeria->descripcion)
                                <p class="mt-2 text-sm text-texto-secundario">{{ Str::limit($galeria->descripcion, 100) }}</p>
                            @endif
                        </div>
                    </x-tarjeta>
                @endforeach
            </div>

            <x-paginacion :paginador="$galerias" class="mt-10" />
        @endif
    </x-seccion>
</x-layouts.publico>
