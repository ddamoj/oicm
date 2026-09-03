<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($titulo) ? $titulo . ' · ' : '' }}Órgano Interno de Control Municipal</title>
        <meta name="description" content="{{ $descripcion ?? 'Micrositio institucional del Órgano Interno de Control Municipal de Oaxaca de Juárez: normatividad, documentos, avisos y trámites.' }}">
        <link rel="icon" href="{{ asset('assets/sitio/images/favicon.png') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="flex min-h-screen flex-col bg-superficie font-body text-texto antialiased">
        {{-- Accesibilidad: primer elemento enfocable, permite saltar la navegación --}}
        <a href="#contenido-principal" class="saltar-contenido">Saltar al contenido principal</a>

        <header class="sticky top-0 z-40 border-b border-borde bg-white/90 backdrop-blur">
            <div x-data="{ menuAbierto: false, buscadorAbierto: false }" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-18 items-center justify-between gap-4 py-3">
                    <a href="{{ route('inicio') }}" class="flex shrink-0 items-center gap-3">
                        <img src="{{ asset('assets/sitio/images/logo-verde-horizontal.png') }}" alt="Municipio de Oaxaca de Juárez" class="h-9 w-auto">
                        <span class="hidden whitespace-nowrap border-l border-borde pl-3 font-sans text-sm font-bold leading-tight tracking-tight text-primario xl:block">
                            Órgano Interno de<br>Control Municipal
                        </span>
                    </a>

                    {{-- Navegación de escritorio --}}
                    <nav aria-label="Principal" class="hidden min-w-0 lg:block">
                        <ul class="flex items-center gap-0.5 whitespace-nowrap text-[13px] font-bold tracking-tight">
                            @foreach (\App\Support\NavegacionPublica::enlaces() as $etiqueta => $ruta)
                                <li>
                                    <a
                                        href="{{ route($ruta) }}"
                                        class="relative block rounded-full px-3 py-2.5 transition-colors duration-150 hover:bg-primario-claro hover:text-primario
                                               {{ request()->routeIs($ruta) ? 'text-primario' : 'text-texto' }}"
                                        @if (request()->routeIs($ruta)) aria-current="page" @endif
                                    >
                                        {{ $etiqueta }}
                                        @if (request()->routeIs($ruta))
                                            <span class="absolute inset-x-3 -bottom-0.5 h-0.5 rounded-full bg-acento-oscuro" aria-hidden="true"></span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                    <div class="flex shrink-0 items-center gap-2">
                        {{-- Botón de búsqueda global (Fase 8) --}}
                        <button
                            type="button"
                            x-on:click="buscadorAbierto = !buscadorAbierto"
                            class="rounded-full p-2.5 text-primario hover:bg-primario-claro"
                            :aria-expanded="buscadorAbierto.toString()"
                            aria-controls="buscador-global-panel"
                            aria-label="Buscar en el micrositio"
                        >
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <x-boton href="{{ route('login') }}" variante="secundario" tamano="sm" class="hidden sm:inline-flex">
                            Acceso institucional
                        </x-boton>

                        {{-- Botón de menú móvil --}}
                        <button
                            type="button"
                            x-on:click="menuAbierto = !menuAbierto"
                            class="rounded-full p-2.5 text-primario hover:bg-primario-claro lg:hidden"
                            :aria-expanded="menuAbierto.toString()"
                            aria-controls="menu-movil"
                            aria-label="Abrir menú de navegación"
                        >
                            <svg x-show="!menuAbierto" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <svg x-show="menuAbierto" x-cloak class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Navegación móvil desplegable --}}
                <nav
                    id="menu-movil"
                    x-show="menuAbierto"
                    x-cloak
                    x-transition:enter="ease-institucional duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    aria-label="Principal (móvil)"
                    class="border-t border-borde pb-4 lg:hidden"
                >
                    <ul class="mt-2 flex flex-col gap-1 text-sm font-semibold">
                        @foreach (\App\Support\NavegacionPublica::enlaces() as $etiqueta => $ruta)
                            <li>
                                <a href="{{ route($ruta) }}" class="block rounded-lg px-4 py-3 hover:bg-primario-claro hover:text-primario {{ request()->routeIs($ruta) ? 'text-primario' : 'text-texto' }}">
                                    {{ $etiqueta }}
                                </a>
                            </li>
                        @endforeach
                        <li class="mt-2">
                            <x-boton href="{{ route('login') }}" variante="primario" tamano="sm" class="w-full">
                                Acceso institucional
                            </x-boton>
                        </li>
                    </ul>
                </nav>

                {{-- Panel de búsqueda global desplegable (Fase 8) --}}
                <div
                    id="buscador-global-panel"
                    x-show="buscadorAbierto"
                    x-cloak
                    x-on:keydown.escape.window="buscadorAbierto = false"
                    x-transition:enter="ease-institucional duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="border-t border-borde py-4"
                >
                    <x-buscador accion="{{ route('buscar') }}" parametro="q" marcador="Buscar noticias, documentos, normatividad…" etiqueta="Buscar en el micrositio" />
                </div>
            </div>
        </header>

        <main id="contenido-principal" class="flex-1">
            {{ $slot }}
        </main>

        <footer class="relative overflow-hidden border-t border-primario-oscuro bg-primario-oscuro text-white/90">
            <x-sello-oicm class="pointer-events-none absolute -right-16 -top-20 size-96 text-white/[0.045]" />

            <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <div class="inline-block rounded-lg bg-white p-2.5">
                            <img src="{{ asset('assets/sitio/images/logo-verde-horizontal.png') }}" alt="Municipio de Oaxaca de Juárez" class="h-8 w-auto">
                        </div>
                        <p class="mt-4 text-sm leading-relaxed text-white/70">
                            Órgano Interno de Control Municipal de Oaxaca de Juárez. Transparencia, legalidad y rendición de cuentas.
                        </p>
                    </div>

                    <div>
                        <h2 class="font-sans text-xs font-bold uppercase tracking-[0.12em] text-acento">Navegación</h2>
                        <ul class="mt-4 space-y-2 text-sm text-white/70">
                            @foreach (\App\Support\NavegacionPublica::enlaces() as $etiqueta => $ruta)
                                <li><a href="{{ route($ruta) }}" class="hover:text-white">{{ $etiqueta }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <h2 class="font-sans text-xs font-bold uppercase tracking-[0.12em] text-acento">Contacto</h2>
                        <ul class="mt-4 space-y-2 text-sm text-white/70">
                            <li>Palacio Municipal, Oaxaca de Juárez, Oax.</li>
                            <li><a href="mailto:contraloria@municipiodeoaxaca.gob.mx" class="hover:text-white">contraloria@municipiodeoaxaca.gob.mx</a></li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="font-sans text-xs font-bold uppercase tracking-[0.12em] text-acento">Canal de quejas y denuncias</h2>
                        <p class="mt-4 text-sm text-white/70">
                            Para reportar irregularidades de personas servidoras públicas municipales, consulta la sección de Contacto.
                        </p>
                    </div>
                </div>

                {{-- Barra legal (Fase 9): fuera de \App\Support\NavegacionPublica porque ese
                     helper también alimenta el menú principal del header. --}}
                <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 text-xs text-white/60 sm:flex-row">
                    <p>&copy; {{ now()->year }} Municipio de Oaxaca de Juárez · Órgano Interno de Control Municipal</p>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('aviso-privacidad') }}" class="hover:text-white">Aviso de privacidad</a>
                        <p>Gobierno Municipal 2025-2027</p>
                    </div>
                </div>
            </div>
        </footer>

        @stack('modals')
        @livewireScripts
    </body>
</html>
