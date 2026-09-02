{{-- Vista de paginación con la identidad visual del OICM (usada por x-paginacion) --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex items-center justify-between gap-4">
        <div class="hidden sm:block">
            <p class="text-sm text-texto-secundario">
                Mostrando <span class="font-semibold text-texto">{{ $paginator->firstItem() }}</span>
                a <span class="font-semibold text-texto">{{ $paginator->lastItem() }}</span>
                de <span class="font-semibold text-texto">{{ $paginator->total() }}</span> resultados
            </p>
        </div>

        <div class="flex flex-1 justify-between gap-2 sm:flex-none">
            @if ($paginator->onFirstPage())
                <span class="inline-flex cursor-not-allowed items-center rounded-full border border-borde px-4 py-2 text-sm text-gris">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded-full border border-borde px-4 py-2 text-sm font-medium text-texto hover:border-primario hover:text-primario">Anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded-full border border-borde px-4 py-2 text-sm font-medium text-texto hover:border-primario hover:text-primario">Siguiente</a>
            @else
                <span class="inline-flex cursor-not-allowed items-center rounded-full border border-borde px-4 py-2 text-sm text-gris">Siguiente</span>
            @endif
        </div>
    </nav>
@endif
