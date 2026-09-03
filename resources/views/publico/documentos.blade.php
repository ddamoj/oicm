<x-layouts.publico titulo="Documentos">
    <x-hero
        titulo="Documentos institucionales"
        subtitulo="Formatos, oficios y bases de datos del OICM, disponibles para consulta y descarga sin necesidad de iniciar sesión."
    />

    <x-seccion antetitulo="Repositorio público">
        <x-migas :items="['Documentos' => null]" class="mb-8" />

        <livewire:publico.lista-documentos-publica />
    </x-seccion>
</x-layouts.publico>
