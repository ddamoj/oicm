<x-layouts.publico titulo="Buscar">
    <x-hero
        titulo="Buscador global"
        subtitulo="Encuentra documentos, noticias, normatividad, enlaces e información institucional en un solo lugar."
    />

    <x-seccion antetitulo="Resultados">
        <x-migas :items="['Buscar' => null]" class="mb-8" />

        <livewire:publico.buscador-global />
    </x-seccion>
</x-layouts.publico>
