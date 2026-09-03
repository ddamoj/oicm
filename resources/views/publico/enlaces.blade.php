<x-layouts.publico titulo="Enlaces de interés">
    <x-hero
        titulo="Enlaces de interés"
        subtitulo="Trámites y organismos vinculados con la función del OICM: declaración patrimonial, evaluación de control interno, entrega-recepción y más."
    />

    <x-seccion antetitulo="Directorio público">
        <x-migas :items="['Enlaces de interés' => null]" class="mb-8" />

        <livewire:publico.lista-enlaces-publica />
    </x-seccion>
</x-layouts.publico>
