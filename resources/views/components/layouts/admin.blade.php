<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($titulo) ? $titulo . ' · ' : '' }}Administración OICM</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="flex min-h-screen bg-superficie-alterna font-body text-texto antialiased" x-data="{ menuAbierto: false }">
        <a href="#contenido-admin" class="saltar-contenido">Saltar al contenido principal</a>

        {{-- Overlay del menú lateral en móvil --}}
        <div
            x-show="menuAbierto"
            x-cloak
            x-transition.opacity
            x-on:click="menuAbierto = false"
            class="fixed inset-0 z-30 bg-texto/40 lg:hidden"
            aria-hidden="true"
        ></div>

        {{-- Navegación lateral, condicionada por el rol de la persona autenticada --}}
        <aside
            id="menu-lateral"
            :class="menuAbierto ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-primario-oscuro text-white transition-transform duration-200 ease-institucional lg:static lg:translate-x-0"
        >
            <div class="flex items-center gap-3 border-b border-white/10 px-6 py-5">
                <div class="rounded-lg bg-white p-2">
                    <img src="{{ asset('assets/sitio/images/logo-verde-horizontal.png') }}" alt="OICM" class="h-6 w-auto">
                </div>
                <span class="font-sans text-sm font-bold leading-tight tracking-tight">Panel de<br>Administración</span>
            </div>

            <nav aria-label="Administración" class="flex-1 overflow-y-auto px-3 py-4">
                <ul class="space-y-1 text-sm font-medium">
                    @foreach (\App\Support\NavegacionAdmin::enlaces(auth()->user()) as $etiqueta => $datos)
                        <li>
                            <a
                                href="{{ route($datos['ruta']) }}"
                                class="flex items-center gap-3 rounded-lg px-3.5 py-2.5 transition-colors duration-150 hover:bg-white/10
                                       {{ request()->routeIs($datos['ruta']) ? 'bg-acento text-primario-oscuro hover:bg-acento' : 'text-white/85' }}"
                                @if (request()->routeIs($datos['ruta'])) aria-current="page" @endif
                            >
                                <span class="flex size-5 items-center justify-center">{!! $datos['icono'] !!}</span>
                                {{ $etiqueta }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="border-t border-white/10 p-4">
                <a href="{{ route('inicio') }}" class="block rounded-lg px-3.5 py-2.5 text-sm font-medium text-white/70 hover:bg-white/10 hover:text-white">
                    ← Volver al micrositio público
                </a>
            </div>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col lg:pl-0">
            {{-- Barra superior --}}
            <header class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-borde bg-white px-4 py-3 sm:px-6">
                <button
                    type="button"
                    x-on:click="menuAbierto = !menuAbierto"
                    class="rounded-full p-2 text-primario hover:bg-primario-claro lg:hidden"
                    :aria-expanded="menuAbierto.toString()"
                    aria-controls="menu-lateral"
                    aria-label="Abrir menú de administración"
                >
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <h1 class="text-lg font-semibold text-texto">{{ $titulo ?? 'Panel de administración' }}</h1>

                <div class="flex items-center gap-3">
                    <span class="hidden text-sm text-texto-secundario sm:block">{{ auth()->user()?->name }}</span>
                    <x-badge variante="primario">{{ auth()->user()?->rol?->nombre ?? 'Sin rol' }}</x-badge>
                </div>
            </header>

            <main id="contenido-admin" class="flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html>
