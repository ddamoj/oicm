<x-layouts.admin :titulo="$titulo">
    <x-migas :items="[$titulo => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <x-tarjeta>
        <x-vacio
            titulo="Módulo en desarrollo"
            descripcion="La gestión de {{ mb_strtolower($titulo) }} se construye en una fase posterior del plan de trabajo."
        >
            <x-slot:icono>
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5V12l3 2" />
                </svg>
            </x-slot:icono>
        </x-vacio>
    </x-tarjeta>
</x-layouts.admin>
