<x-layouts.publico titulo="Quiénes somos">
    <x-hero
        titulo="Quiénes somos"
        subtitulo="Historia, misión, visión, valores y estructura orgánica del Órgano Interno de Control Municipal."
    />

    <x-seccion ancho="max-w-3xl">
        <x-migas :items="['Quiénes somos' => null]" class="mb-8" />

        @if ($pagina)
            <div class="texto-justificado prose prose-lg max-w-none text-texto prose-headings:text-texto prose-a:text-primario">
                {!! $pagina->contenido !!}
            </div>
        @else
            <x-vacio titulo="Contenido en preparación" descripcion="Esta información se está cargando. Vuelve pronto." />
        @endif
    </x-seccion>

    <x-seccion antetitulo="Organigrama" titulo="Estructura orgánica" descripcion="Un Contralor, cuatro Direcciones de área y sus departamentos." alterna>
        <div class="space-y-4" x-data="{ abierta: null }">
            @foreach ($direcciones as $direccion)
                <div class="overflow-hidden rounded-xl border border-borde bg-superficie shadow-suave">
                    <h3>
                        <button
                            type="button"
                            x-on:click="abierta = (abierta === {{ $direccion->id }} ? null : {{ $direccion->id }})"
                            x-bind:aria-expanded="(abierta === {{ $direccion->id }}).toString()"
                            aria-controls="departamentos-{{ $direccion->id }}"
                            class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                        >
                            <span>
                                <span class="block text-lg font-bold tracking-tight text-texto">{{ $direccion->nombre }}</span>
                                @if ($direccion->siglas)
                                    <span class="text-xs font-semibold uppercase tracking-wide text-texto-secundario">{{ $direccion->siglas }}</span>
                                @endif
                            </span>

                            <svg
                                class="size-5 shrink-0 text-texto-secundario transition-transform duration-200"
                                x-bind:class="abierta === {{ $direccion->id }} ? 'rotate-180' : ''"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                            >
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </h3>

                    <div
                        id="departamentos-{{ $direccion->id }}"
                        x-show="abierta === {{ $direccion->id }}"
                        x-transition:enter="ease-institucional duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-cloak
                    >
                        <div class="border-t border-borde px-6 py-5">
                            @if ($direccion->descripcion)
                                <p class="texto-justificado mb-4 text-sm leading-relaxed text-texto-secundario">{{ $direccion->descripcion }}</p>
                            @endif

                            @if ($direccion->departamentos->isNotEmpty())
                                <ul class="grid gap-2 sm:grid-cols-2">
                                    @foreach ($direccion->departamentos as $departamento)
                                        <li class="flex items-start gap-2 text-sm text-texto">
                                            <svg class="mt-0.5 size-4 shrink-0 text-primario" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $departamento->nombre }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <x-boton href="{{ route('direcciones.mostrar', $direccion) }}" variante="secundario" tamano="sm" class="mt-5">
                                Ver la Dirección
                            </x-boton>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-6 text-xs text-texto-secundario">
            Los nombres de los departamentos están pendientes de confirmación oficial por el OICM.
        </p>
    </x-seccion>
</x-layouts.publico>
