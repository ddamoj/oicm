<x-layouts.publico :titulo="$titulo">
    <x-hero :titulo="$titulo" subtitulo="Esta sección está en construcción. El contenido institucional se incorporará en las siguientes fases del proyecto." />

    <x-seccion>
        <x-migas :items="[$titulo => null]" class="mb-8" />

        <x-vacio
            titulo="Contenido en preparación"
            descripcion="Estamos cargando la información oficial de esta sección junto con las Direcciones del OICM. Vuelve pronto."
        >
            <x-boton href="{{ route('inicio') }}" variante="secundario">Volver al inicio</x-boton>
        </x-vacio>
    </x-seccion>
</x-layouts.publico>
