<x-layouts.publico titulo="Página no encontrada">
    <x-seccion>
        <div class="mx-auto max-w-lg py-8 text-center">
            <p class="font-sans text-sm font-semibold uppercase tracking-wide text-primario">Error 404</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">No encontramos esta página</h1>
            <p class="mt-4 text-texto-secundario">
                Es posible que la dirección esté mal escrita o que el contenido haya cambiado de ubicación.
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <x-boton href="{{ route('inicio') }}">Ir al inicio</x-boton>
                <x-boton href="{{ route('documentos') }}" variante="secundario">Buscar documentos</x-boton>
            </div>
        </div>
    </x-seccion>
</x-layouts.publico>
