@props([
    'items' => [], // arreglo asociativo ['Etiqueta' => 'url'] — la última entrada puede llevar url null (página actual)
    'raiz' => 'inicio', // nombre de ruta de la raíz (público: "inicio"; admin: "admin.panel")
    'etiquetaRaiz' => 'Inicio',
])

<nav aria-label="Ruta de navegación" {{ $attributes->merge(['class' => 'text-sm']) }}>
    <ol class="flex flex-wrap items-center gap-1.5 text-texto-secundario">
        <li class="flex items-center gap-1.5">
            <a href="{{ route($raiz) }}" class="rounded hover:text-primario">{{ $etiquetaRaiz }}</a>
        </li>

        @foreach ($items as $etiqueta => $url)
            <li class="flex items-center gap-1.5">
                <svg class="size-3.5 text-gris" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>

                @if ($url)
                    <a href="{{ $url }}" class="rounded hover:text-primario">{{ $etiqueta }}</a>
                @else
                    <span class="font-medium text-texto" aria-current="page">{{ $etiqueta }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
