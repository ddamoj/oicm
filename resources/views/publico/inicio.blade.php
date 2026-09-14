<x-layouts.publico>
    <x-hero
        :alto="true"
        titulo="Transparencia y control interno al alcance de todos"
        resaltar="control interno"
        subtitulo="El Órgano Interno de Control Municipal de Oaxaca de Juárez concentra en un solo espacio la normatividad, los documentos, avisos y trámites institucionales."
    >
        <div class="flex flex-col gap-3 sm:flex-row">
            <x-boton href="{{ route('documentos') }}" variante="acento">Consultar documentos</x-boton>
            <x-boton href="{{ route('normatividad') }}" variante="secundario" class="!border-white !text-white hover:!bg-white/10">
                Ver normatividad
            </x-boton>
        </div>

        <div class="mt-8 max-w-xl">
            <x-buscador accion="{{ route('buscar') }}" parametro="q" marcador="Buscar en el micrositio…" etiqueta="Buscar en el micrositio" class="[&_input]:!bg-white/95" />
        </div>
    </x-hero>

    <x-seccion
        antetitulo="Servicio al ciudadano"
        titulo="Acceso rápido"
        descripcion="Los trámites y consultas que con más frecuencia solicitan las personas servidoras públicas y la ciudadanía."
    >
        @php
            $iconosAccesoRapido = [
                'M9 12.75h6m-6 3h4.5m-1.5-16.5H6.75A2.25 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25h10.5A2.25 2.25 0 0019.5 19.5V11.25m-7.5-9.75l7.5 7.5m-7.5-7.5v6a1.5 1.5 0 001.5 1.5h6',
                'M12 3v18m0-18L5.25 6.75m6.75-3.75L18.75 6.75M3 10.5l2.25-3.75L7.5 10.5m-4.5 0h4.5m-4.5 0a2.25 2.25 0 002.25 2.25A2.25 2.25 0 007.5 10.5M16.5 10.5l2.25-3.75 2.25 3.75m-4.5 0h4.5m-4.5 0a2.25 2.25 0 002.25 2.25 2.25 2.25 0 002.25-2.25M4.5 20.25h15',
                'M13.5 10.5l6-6m0 0h-4.5m4.5 0v4.5M11.25 6H6.75A2.25 2.25 0 004.5 8.25v9A2.25 2.25 0 006.75 19.5h9a2.25 2.25 0 002.25-2.25v-4.5',
            ];
            $acentosAccesoRapido = ['primario', 'acento', 'neutro'];
        @endphp

        @if ($accesosRapidos->isEmpty())
            {{-- Respaldo estático si el Administrador de Contenido aún no marca ningún enlace como acceso rápido. --}}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-tarjeta href="{{ route('documentos') }}" :flotante="true" acento="primario">
                    <x-badge variante="primario" :punto="true">Formatos y oficios</x-badge>
                    <h3 class="mt-4 text-lg font-bold tracking-tight">Documentos institucionales</h3>
                    <p class="texto-justificado mt-2 text-sm text-texto-secundario">Formatos, oficios y bases de datos, organizados por categoría y disponibles sin necesidad de iniciar sesión.</p>
                </x-tarjeta>

                <x-tarjeta href="{{ route('normatividad') }}" :flotante="true" acento="acento">
                    <x-badge variante="acento" :punto="true">Marco legal</x-badge>
                    <h3 class="mt-4 text-lg font-bold tracking-tight">Normatividad aplicable</h3>
                    <p class="texto-justificado mt-2 text-sm text-texto-secundario">Leyes, reglamentos y lineamientos de ámbito federal, estatal y municipal que rigen la actuación del OICM.</p>
                </x-tarjeta>

                <x-tarjeta href="{{ route('enlaces') }}" :flotante="true" acento="neutro">
                    <x-badge :punto="true">Trámites externos</x-badge>
                    <h3 class="mt-4 text-lg font-bold tracking-tight">Enlaces de interés</h3>
                    <p class="texto-justificado mt-2 text-sm text-texto-secundario">Declaración patrimonial, evaluación de control interno, entrega-recepción y otros trámites vinculados.</p>
                </x-tarjeta>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($accesosRapidos as $indice => $enlace)
                    <x-tarjeta wire:key="acceso-{{ $enlace->id }}" href="{{ $enlace->url }}" target="_blank" rel="noopener noreferrer" :flotante="true" :acento="$acentosAccesoRapido[$indice % 3]">
                        <x-slot:icono>
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconosAccesoRapido[$indice % 3] }}" />
                            </svg>
                        </x-slot:icono>
                        <x-badge :variante="$acentosAccesoRapido[$indice % 3]" :punto="true">Trámite externo</x-badge>
                        <h3 class="mt-4 text-lg font-bold tracking-tight">{{ $enlace->nombre }}</h3>
                        @if ($enlace->descripcion)
                            <p class="texto-justificado mt-2 text-sm text-texto-secundario">{{ $enlace->descripcion }}</p>
                        @endif
                    </x-tarjeta>
                @endforeach
            </div>
        @endif
    </x-seccion>

    <x-seccion antetitulo="Comunicación institucional" titulo="Últimas noticias" descripcion="Comunicados y avisos publicados por el OICM." :alterna="true">
        @if ($ultimasNoticias->isEmpty())
            <x-vacio
                titulo="Aún no hay noticias publicadas"
                descripcion="En cuanto el OICM publique un comunicado, aparecerá aquí de forma automática."
            />
        @else
            <div class="grid gap-6 sm:grid-cols-3">
                @foreach ($ultimasNoticias as $noticia)
                    <x-tarjeta wire:key="inicio-noticia-{{ $noticia->id }}" href="{{ route('noticias.mostrar', $noticia) }}" flotante class="!p-0 overflow-hidden">
                        <div class="aspect-[3/2] w-full overflow-hidden bg-gris-claro">
                            @if ($noticia->urlMiniatura())
                                <img src="{{ $noticia->urlMiniatura() }}" alt="{{ $noticia->imagen_alt }}" loading="lazy" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-semibold uppercase tracking-wide text-texto-secundario">
                                {{ $noticia->publicado_en?->translatedFormat('d \d\e F \d\e Y') }}
                            </p>
                            <h3 class="mt-2 text-lg font-bold tracking-tight text-texto">{{ $noticia->titulo }}</h3>
                        </div>
                    </x-tarjeta>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <x-boton href="{{ route('noticias') }}" variante="secundario">Ver todas las noticias</x-boton>
            </div>
        @endif
    </x-seccion>

    <x-seccion antetitulo="Repositorio público" titulo="Documentos recientes" descripcion="Últimos formatos, oficios y bases de datos incorporados al repositorio.">
        @if ($documentosRecientes->isEmpty())
            <x-vacio
                titulo="Aún no hay documentos publicados"
                descripcion="En cuanto el OICM publique un documento, aparecerá aquí de forma automática."
            />
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($documentosRecientes as $documento)
                    <x-tarjeta wire:key="inicio-documento-{{ $documento->id }}" href="{{ route('documentos') }}" :flotante="true" acento="primario">
                        <x-slot:icono>
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H8.25m5.231 13.481L15 14.25m0 0l-2.25 2.25M15 14.25v6M6.75 21h10.5a2.25 2.25 0 002.25-2.25V9.75L15 3H6.75a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006.75 21z" />
                            </svg>
                        </x-slot:icono>
                        <h3 class="mt-2 text-sm font-bold tracking-tight text-texto">{{ $documento->nombre }}</h3>
                        <p class="mt-1 text-xs text-texto-secundario">{{ $documento->categoria?->nombre }}</p>
                    </x-tarjeta>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <x-boton href="{{ route('documentos') }}" variante="secundario">Ver todos los documentos</x-boton>
            </div>
        @endif
    </x-seccion>
</x-layouts.publico>
