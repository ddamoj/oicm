<div>
    <x-tarjeta>
        <h2 class="text-lg font-semibold text-texto">Datos personales</h2>
        <p class="mt-1 text-sm text-texto-secundario">Tu nombre y correo institucional.</p>

        <form wire:submit="guardar" class="mt-6 space-y-4">
            <div>
                <label for="perfil-name" class="mb-1.5 block text-sm font-semibold text-texto">Nombre completo</label>
                <input
                    id="perfil-name"
                    type="text"
                    wire:model="name"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('name') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="perfil-email" class="mb-1.5 block text-sm font-semibold text-texto">Correo institucional</label>
                <input
                    id="perfil-email"
                    type="email"
                    wire:model="email"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('email') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-1">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar">
                    <span wire:loading.remove wire:target="guardar">Guardar cambios</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-tarjeta>
</div>
