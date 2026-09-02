<x-layouts.admin :titulo="$titulo">
    <x-migas :items="[$titulo => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <x-tarjeta>
        <x-vacio
            titulo="Módulo en desarrollo"
            descripcion="La gestión de {{ mb_strtolower($titulo) }} se construye en una fase posterior del plan de trabajo."
        />
    </x-tarjeta>
</x-layouts.admin>
