@php
    $resultados = $this->resultados();
    $total = $this->totalResultados();
@endphp

<div>
    <div class="mx-auto mb-10 max-w-xl">
        <x-buscador accion="{{ route('buscar') }}" parametro="q" :valor="$termino" marcador="Buscar noticias, documentos, normatividad…" etiqueta="Buscar en el micrositio" />
    </div>

    @if (mb_strlen(trim($termino)) > 0 && mb_strlen(trim($termino)) < 3)
        <x-vacio titulo="Escribe al menos 3 caracteres" descripcion="Para obtener resultados relevantes, ingresa un término de búsqueda más largo." />
    @elseif ($termino === '')
        <x-vacio titulo="Empieza tu búsqueda" descripcion="Encuentra documentos, noticias, normatividad, enlaces e información institucional en un solo lugar." />
    @elseif ($total === 0)
        <x-vacio titulo="Sin resultados" descripcion="No encontramos coincidencias para tu búsqueda. Intenta con otras palabras." />
    @else
        <p class="mb-6 text-sm text-texto-secundario">{{ $total }} {{ Str::plural('resultado', $total) }} encontrados.</p>

        <div class="space-y-10">
            @foreach ($secciones as $clave => $config)
                @continue($resultados[$clave]->isEmpty())
                <div wire:key="seccion-{{ $clave }}">
                    <h2 class="mb-4 text-lg font-bold tracking-tight text-texto">{{ $config['etiqueta'] }}</h2>
                    <ul class="space-y-2">
                        @foreach ($resultados[$clave] as $item)
                            <li>
                                <a href="{{ $config['ruta']($item) }}" @if ($clave === 'enlaces') target="_blank" rel="noopener noreferrer" @endif class="block rounded-lg border border-borde bg-superficie px-5 py-3 text-sm font-medium text-texto transition-colors hover:border-primario hover:bg-primario-claro">
                                    {{ $config['titulo']($item) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</div>
