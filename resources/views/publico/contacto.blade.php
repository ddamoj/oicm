<x-layouts.publico titulo="Contacto">
    <x-hero
        titulo="Contacto"
        subtitulo="Directorio por Dirección, ubicación, horarios de atención y canal de quejas y denuncias del OICM."
    />

    <x-seccion antetitulo="Directorio institucional">
        <x-migas :items="['Contacto' => null]" class="mb-8" />

        @if ($canalQuejas)
            <div class="mb-10 flex flex-col gap-4 rounded-xl border border-advertencia/30 bg-advertencia-suave p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <x-badge variante="advertencia" :punto="true">Quejas y denuncias</x-badge>
                    <h2 class="mt-2 text-lg font-bold tracking-tight text-texto">¿Detectaste una irregularidad?</h2>
                    <p class="texto-justificado mt-1 text-sm text-texto-secundario">Repórtala a través del canal de la Dirección de Quejas, Denuncias, Investigación y Situación Patrimonial:</p>
                </div>
                <p class="shrink-0 rounded-lg bg-white px-5 py-3 text-sm font-semibold text-primario-oscuro">{{ $canalQuejas->canal_quejas_denuncias }}</p>
            </div>
        @endif

        @if ($contactos->isEmpty())
            <x-vacio titulo="Directorio en preparación" descripcion="El OICM está actualizando la información de contacto de sus Direcciones." />
        @else
            <div class="grid gap-6 lg:grid-cols-2">
                @foreach ($contactos as $contacto)
                    <x-tarjeta wire:key="contacto-{{ $contacto->id }}">
                        <h3 class="text-lg font-bold tracking-tight text-texto">{{ $contacto->nombre_area }}</h3>
                        @if ($contacto->direccion)
                            <p class="mt-0.5 text-xs font-semibold uppercase tracking-wide text-texto-secundario">{{ $contacto->direccion->nombre }}</p>
                        @endif

                        <dl class="mt-4 space-y-2 text-sm text-texto-secundario">
                            @if ($contacto->domicilio)
                                <div class="flex gap-2">
                                    <dt class="sr-only">Domicilio</dt>
                                    <dd>{{ $contacto->domicilio }}</dd>
                                </div>
                            @endif
                            @if ($contacto->telefono)
                                <div class="flex gap-2">
                                    <dt class="sr-only">Teléfono</dt>
                                    <dd><a href="tel:{{ $contacto->telefono }}" class="hover:text-primario">{{ $contacto->telefono }}</a></dd>
                                </div>
                            @endif
                            @if ($contacto->correo)
                                <div class="flex gap-2">
                                    <dt class="sr-only">Correo</dt>
                                    <dd><a href="mailto:{{ $contacto->correo }}" class="hover:text-primario">{{ $contacto->correo }}</a></dd>
                                </div>
                            @endif
                            @if ($contacto->horario)
                                <div class="flex gap-2">
                                    <dt class="sr-only">Horario</dt>
                                    <dd>{{ $contacto->horario }}</dd>
                                </div>
                            @endif
                        </dl>

                        @if ($contacto->tieneMapa())
                            <div class="mt-5 overflow-hidden rounded-lg border border-borde">
                                {{-- Iframe de Google Maps sin API key; requiere que la Fase 9 permita
                                     frame-src https://www.google.com en el CSP. --}}
                                <iframe
                                    src="https://www.google.com/maps?q={{ $contacto->latitud }},{{ $contacto->longitud }}&output=embed"
                                    class="h-56 w-full"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Mapa de ubicación de {{ $contacto->nombre_area }}"
                                ></iframe>
                            </div>
                            <a
                                href="https://www.google.com/maps/dir/?api=1&destination={{ $contacto->latitud }},{{ $contacto->longitud }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-primario hover:underline"
                            >
                                Cómo llegar
                                <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z" clip-rule="evenodd" /></svg>
                            </a>
                        @endif
                    </x-tarjeta>
                @endforeach
            </div>
        @endif
    </x-seccion>
</x-layouts.publico>
