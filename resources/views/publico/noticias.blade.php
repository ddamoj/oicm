<x-layouts.publico titulo="Noticias">
    <x-hero
        titulo="Noticias y comunicados"
        subtitulo="Avisos oficiales, comunicados y novedades del Órgano Interno de Control Municipal."
    />

    <x-seccion antetitulo="Sala de prensa">
        <x-migas :items="['Noticias' => null]" class="mb-8" />

        <livewire:publico.lista-noticias />
    </x-seccion>
</x-layouts.publico>
