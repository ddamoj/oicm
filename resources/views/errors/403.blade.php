<x-layouts.publico titulo="Acceso no autorizado">
    <x-seccion>
        <div class="mx-auto max-w-lg py-8 text-center">
            <p class="font-sans text-sm font-semibold uppercase tracking-wide text-error">Error 403</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">No tienes permisos para ver esto</h1>
            <p class="mt-4 text-texto-secundario">
                Tu cuenta no cuenta con el rol necesario para acceder a esta sección administrativa.
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <x-boton href="{{ route('inicio') }}">Ir al inicio</x-boton>
            </div>
        </div>
    </x-seccion>
</x-layouts.publico>
