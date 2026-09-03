<x-layouts.publico titulo="Estrados digitales">
    <x-hero
        titulo="Estrados digitales"
        subtitulo="Notificaciones de la Dirección de Responsabilidades Administrativas, Controversias y Sanciones (DRACS), numeradas y con constancia de publicación."
    />

    <x-seccion antetitulo="DRACS">
        <x-migas :items="['Direcciones' => route('direcciones'), 'Estrados digitales' => null]" class="mb-8" />

        <livewire:publico.lista-estrados-publica />
    </x-seccion>
</x-layouts.publico>
