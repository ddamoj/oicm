<x-layouts.admin titulo="Mi perfil">
    <x-migas :items="['Mi perfil' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-texto">Mi perfil</h1>
        <p class="mt-1 text-sm text-texto-secundario">Actualiza tus datos personales o cambia tu contraseña.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <livewire:perfil.datos-personales />
        <livewire:perfil.cambiar-contrasena />
    </div>
</x-layouts.admin>
