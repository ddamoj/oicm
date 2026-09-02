<x-layouts.publico titulo="Error del servidor">
    <x-seccion>
        <div class="mx-auto max-w-lg py-8 text-center">
            <p class="font-sans text-sm font-semibold uppercase tracking-wide text-error">Error 500</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Algo salió mal de nuestro lado</h1>
            <p class="mt-4 text-texto-secundario">
                El equipo técnico ya fue notificado. Intenta de nuevo en unos minutos.
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <x-boton href="{{ route('inicio') }}">Ir al inicio</x-boton>
            </div>
        </div>
    </x-seccion>
</x-layouts.publico>
