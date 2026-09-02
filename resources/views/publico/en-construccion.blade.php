<x-layouts.publico :titulo="$titulo">
    <x-hero :titulo="$titulo" subtitulo="Esta sección está en construcción. El contenido institucional se incorporará en las siguientes fases del proyecto." />

    <x-seccion antetitulo="Próximamente">
        <x-migas :items="[$titulo => null]" class="mb-8" />

        <x-vacio
            titulo="Contenido en preparación"
            descripcion="Estamos cargando la información oficial de esta sección junto con las Direcciones del OICM. Vuelve pronto."
        >
            <x-slot:icono>
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5V12l3 2" />
                </svg>
            </x-slot:icono>

            <x-boton href="{{ route('inicio') }}" variante="secundario">Volver al inicio</x-boton>
        </x-vacio>
    </x-seccion>
</x-layouts.publico>
