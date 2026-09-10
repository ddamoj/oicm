@php
    // Saludo según la hora local: pequeño gesto de cercanía en la primera
    // pantalla que ve la persona al entrar.
    $hora = now()->hour;
    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    $usuario = auth()->user();
    // Solo el nombre de pila: el saludo completo resulta acartonado.
    $nombreCorto = \Illuminate\Support\Str::of($usuario?->name ?? '')->trim()->explode(' ')->first();
@endphp

<div>
    {{-- Encabezado de bienvenida --}}
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-texto-secundario">
            {{ ucfirst(now()->translatedFormat('l, j \d\e F \d\e Y')) }}
        </p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-texto">
            {{ $saludo }}{{ $nombreCorto ? ', '.$nombreCorto : '' }}
        </h1>
        <p class="mt-1.5 text-sm text-texto-secundario">
            Estado del micrositio del Órgano Interno de Control Municipal.
        </p>
    </div>

    {{-- Contenido publicado: lo que la ciudadanía ve ahora mismo --}}
    @if (! empty($this->publicado))
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Publicado en el portal</h2>

        <div class="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($this->publicado as $metrica)
                <x-tarjeta :href="route($metrica['ruta'])" acento="primario">
                    <p class="text-sm font-semibold text-texto-secundario">{{ $metrica['etiqueta'] }}</p>
                    <p class="mt-2 text-4xl font-bold tracking-tight text-primario">{{ number_format($metrica['total']) }}</p>
                    <p class="mt-1 text-xs text-texto-secundario">{{ $metrica['detalle'] }}</p>
                </x-tarjeta>
            @endforeach
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Pendientes: la parte accionable del panel --}}
        <div class="lg:col-span-2">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Requiere atención</h2>

            @if (empty($this->pendientes))
                <x-vacio
                    titulo="Todo al día"
                    descripcion="No hay borradores ni material sin publicar. Todo el contenido cargado está visible en el portal."
                >
                    <x-slot:icono>
                        <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot:icono>
                </x-vacio>
            @else
                <x-tarjeta class="!p-0">
                    <ul class="divide-y divide-borde">
                        @foreach ($this->pendientes as $pendiente)
                            <li>
                                <a
                                    href="{{ route($pendiente['ruta']) }}"
                                    class="flex items-center justify-between gap-4 px-6 py-4 transition-colors duration-200 hover:bg-superficie-alterna"
                                >
                                    <span class="flex items-center gap-3">
                                        <x-badge :variante="$pendiente['variante']" :punto="true">Pendiente</x-badge>
                                        <span class="text-sm font-medium text-texto">{{ $pendiente['texto'] }}</span>
                                    </span>

                                    <svg class="size-4 shrink-0 text-gris-oscuro" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-tarjeta>
            @endif

            {{-- Actividad reciente: solo para quien puede consultar la bitácora --}}
            @if ($this->actividad->isNotEmpty())
                <div class="mt-8 flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-texto-secundario">Actividad reciente</h2>
                    <a href="{{ route('admin.bitacora') }}" class="text-xs font-semibold text-primario hover:underline">Ver bitácora completa</a>
                </div>

                <x-tarjeta class="mt-3 !p-0">
                    <ul class="divide-y divide-borde">
                        @foreach ($this->actividad as $registro)
                            <li class="flex items-start justify-between gap-4 px-6 py-3.5">
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium text-texto">
                                        {{ $this->etiquetaAccion($registro->accion) }}
                                    </span>
                                    <span class="block text-xs text-texto-secundario">
                                        {{ $this->autorDe($registro) }}
                                        @if (! empty($registro->detalle['titulo']) || ! empty($registro->detalle['nombre']))
                                            · {{ Str::limit($registro->detalle['titulo'] ?? $registro->detalle['nombre'], 45) }}
                                        @endif
                                    </span>
                                </span>

                                <time
                                    class="shrink-0 text-xs text-texto-secundario"
                                    datetime="{{ $registro->creado_en?->toIso8601String() }}"
                                    title="{{ $registro->creado_en?->format('d/m/Y H:i') }}"
                                >
                                    {{ $registro->creado_en?->diffForHumans(short: true) }}
                                </time>
                            </li>
                        @endforeach
                    </ul>
                </x-tarjeta>
            @endif
        </div>

        {{-- Columna lateral: cuentas y accesos rápidos --}}
        <div class="space-y-6">
            @if ($this->cuentas)
                <div>
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Cuentas con acceso</h2>

                    <x-tarjeta :href="route('admin.usuarios')" acento="acento">
                        <p class="text-4xl font-bold tracking-tight text-primario">{{ $this->cuentas['activas'] }}</p>
                        <p class="mt-1 text-sm text-texto-secundario">
                            {{ $this->cuentas['activas'] === 1 ? 'cuenta activa' : 'cuentas activas' }}
                            @if ($this->cuentas['inactivas'] > 0)
                                · {{ $this->cuentas['inactivas'] }} desactivada{{ $this->cuentas['inactivas'] === 1 ? '' : 's' }}
                            @endif
                        </p>
                    </x-tarjeta>
                </div>
            @endif

            {{-- Pulso de visitas: el detalle vive en el tablero de estadísticas. --}}
            <div>
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Visitas al portal</h2>

                <x-tarjeta :href="route('admin.estadisticas')" acento="primario">
                    <p class="text-4xl font-bold tracking-tight text-primario">{{ number_format($this->visitas['hoy']) }}</p>
                    <p class="mt-1 text-sm text-texto-secundario">
                        {{ $this->visitas['hoy'] === 1 ? 'visita hoy' : 'visitas hoy' }}
                        · {{ number_format($this->visitas['mes']) }} este mes
                    </p>
                    <p class="mt-3 text-xs font-semibold text-primario">Ver estadísticas completas &rarr;</p>
                </x-tarjeta>
            </div>

            <div>
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Accesos rápidos</h2>

                <x-tarjeta class="!p-0">
                    <ul class="divide-y divide-borde">
                        @foreach (\App\Support\NavegacionAdmin::enlaces(auth()->user()) as $etiqueta => $datos)
                            @continue($datos['ruta'] === 'admin.panel')

                            <li>
                                <a
                                    href="{{ route($datos['ruta']) }}"
                                    class="flex items-center gap-3 px-5 py-3 text-sm font-medium text-texto transition-colors duration-200 hover:bg-superficie-alterna hover:text-primario"
                                >
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primario-claro text-primario [&>svg]:size-4">
                                        {!! $datos['icono'] !!}
                                    </span>
                                    {{ $etiqueta }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-tarjeta>
            </div>

            <x-tarjeta href="{{ route('inicio') }}" acento="neutro">
                <p class="text-sm font-semibold text-texto">Ver el micrositio público</p>
                <p class="mt-1 text-xs text-texto-secundario">Revisa cómo se ve el contenido publicado para la ciudadanía.</p>
            </x-tarjeta>
        </div>
    </div>
</div>
