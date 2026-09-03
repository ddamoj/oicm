<x-layouts.publico titulo="Normatividad">
    <x-hero
        titulo="Normatividad"
        subtitulo="Marco jurídico federal, estatal y municipal que rige la actuación del Órgano Interno de Control Municipal, con su fecha de publicación y última reforma."
    />

    <x-seccion antetitulo="Marco normativo">
        <x-migas :items="['Normatividad' => null]" class="mb-8" />

        <livewire:publico.lista-normatividad-publica />
    </x-seccion>
</x-layouts.publico>
