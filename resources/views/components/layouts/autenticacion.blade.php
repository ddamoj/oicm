<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($titulo) ? $titulo . ' · ' : '' }}Acceso institucional · OICM</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="flex min-h-screen flex-col bg-superficie font-body text-texto antialiased lg:flex-row">
        <a href="#contenido-autenticacion" class="saltar-contenido">Saltar al contenido principal</a>

        {{-- Panel institucional: se oculta en móvil para dejar el formulario al frente --}}
        <div class="aurora-oicm relative hidden overflow-hidden lg:flex lg:w-2/5 lg:flex-col lg:justify-between lg:p-12 xl:w-1/2">
            <x-sello-oicm class="pointer-events-none absolute -bottom-24 -right-24 size-96 text-white/[0.06]" />

            <a href="{{ route('inicio') }}" class="relative flex items-center gap-3">
                <div class="rounded-lg bg-white p-2">
                    <img src="{{ asset('assets/sitio/images/logo-verde-horizontal.png') }}" alt="Municipio de Oaxaca de Juárez" class="h-7 w-auto">
                </div>
                <span class="font-sans text-sm font-bold leading-tight tracking-tight text-white">
                    Órgano Interno de<br>Control Municipal
                </span>
            </a>

            <div class="relative max-w-md">
                <p class="antetitulo text-acento">Acceso institucional</p>
                <h1 class="mt-3 text-4xl font-bold leading-[1.05] tracking-tight text-white xl:text-5xl">
                    Panel de administración del OICM
                </h1>
                <p class="mt-4 text-white/75">
                    Espacio restringido para personas administradoras del micrositio. El acceso público
                    a normatividad, documentos y avisos no requiere iniciar sesión.
                </p>
            </div>

            <p class="relative text-xs text-white/50">© {{ now()->year }} Órgano Interno de Control Municipal · Municipio de Oaxaca de Juárez</p>
        </div>

        <div id="contenido-autenticacion" class="flex flex-1 flex-col items-center justify-center px-4 py-12 sm:px-6">
            {{-- Lockup visible solo en móvil, donde el panel institucional está oculto --}}
            <a href="{{ route('inicio') }}" class="mb-8 flex items-center gap-3 lg:hidden">
                <img src="{{ asset('assets/sitio/images/logo-verde-horizontal.png') }}" alt="Municipio de Oaxaca de Juárez" class="h-8 w-auto">
            </a>

            <div class="w-full max-w-sm">
                {{ $slot }}
            </div>
        </div>

        {{-- Mensajería del módulo de autenticación vía SweetAlert2 (nunca alertas Blade
             sueltas). El mensaje viaja en atributos data-* porque, desde la Fase 9, la
             Content-Security-Policy no admite <script> inline: resources/js/app.js lo
             lee en DOMContentLoaded y despacha la alerta correspondiente. --}}
        @if ($errors->any() || session('status'))
            <div
                id="mensajes-autenticacion"
                hidden
                @if ($errors->any()) data-error="{{ $errors->first() }}" @endif
                @if (session('status')) data-status="{{ session('status') }}" @endif
            ></div>
        @endif

        @stack('modals')
        @livewireScripts
    </body>
</html>
