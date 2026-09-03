<x-layouts.publico :titulo="$direccion->nombre">
    <x-hero :titulo="$direccion->nombre" :subtitulo="$direccion->descripcion" />

    <x-seccion ancho="max-w-3xl">
        <x-migas :items="['Direcciones' => route('direcciones'), $direccion->nombre => null]" class="mb-8" />

        @if ($pagina)
            <div class="prose prose-lg max-w-none text-texto prose-headings:text-texto prose-a:text-primario">
                {!! $pagina->contenido !!}
            </div>
        @endif

        @if ($departamentos->isNotEmpty())
            <div class="mt-10">
                <h2 class="text-xl font-bold tracking-tight text-texto">Departamentos</h2>
                <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                    @foreach ($departamentos as $departamento)
                        <li class="flex items-start gap-2 text-sm text-texto">
                            <svg class="mt-0.5 size-4 shrink-0 text-primario" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                            {{ $departamento->nombre }}
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-xs text-texto-secundario">Los nombres de los departamentos están pendientes de confirmación oficial por el OICM.</p>
            </div>
        @endif

        <div class="mt-10 flex flex-wrap gap-3 border-t border-borde pt-8">
            <x-boton href="{{ route('documentos', ['direccion' => $direccion->id]) }}" variante="primario">
                Formatos y oficios de esta Dirección
            </x-boton>

            @if ($direccion->clave === 'responsabilidades-controversias-sanciones')
                <x-boton href="{{ route('estrados') }}" variante="secundario">
                    Estrados digitales
                </x-boton>
            @endif
        </div>
    </x-seccion>
</x-layouts.publico>
