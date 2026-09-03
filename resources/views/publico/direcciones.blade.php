<x-layouts.publico titulo="Direcciones">
    <x-hero
        titulo="Direcciones"
        subtitulo="La Oficina del Contralor y las cuatro direcciones de área que integran el Órgano Interno de Control Municipal."
    />

    <x-seccion antetitulo="Estructura">
        <x-migas :items="['Direcciones' => null]" class="mb-8" />

        @if ($direcciones->isEmpty())
            <x-vacio titulo="Contenido en preparación" descripcion="Esta información se está cargando. Vuelve pronto." />
        @else
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($direcciones as $direccion)
                    <x-tarjeta href="{{ route('direcciones.mostrar', $direccion) }}" flotante acento="primario">
                        <x-slot:icono>
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </x-slot:icono>

                        @if ($direccion->siglas)
                            <x-badge variante="primario">{{ $direccion->siglas }}</x-badge>
                        @endif

                        <h3 class="mt-3 text-lg font-bold tracking-tight text-texto">{{ $direccion->nombre }}</h3>

                        @if ($direccion->descripcion)
                            <p class="mt-2 text-sm leading-relaxed text-texto-secundario">{{ $direccion->descripcion }}</p>
                        @endif
                    </x-tarjeta>
                @endforeach
            </div>
        @endif
    </x-seccion>
</x-layouts.publico>
