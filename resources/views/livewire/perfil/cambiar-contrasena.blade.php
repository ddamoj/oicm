<div>
    <x-tarjeta>
        <h2 class="text-lg font-semibold text-texto">Contraseña</h2>
        <p class="mt-1 text-sm text-texto-secundario">Mínimo 10 caracteres, con mayúsculas, minúsculas, números y símbolos.</p>

        <form wire:submit="guardar" class="mt-6 space-y-4">
            <div>
                <label for="perfil-current-password" class="mb-1.5 block text-sm font-semibold text-texto">Contraseña actual</label>
                <input
                    id="perfil-current-password"
                    type="password"
                    wire:model="current_password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('current_password') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="perfil-password" class="mb-1.5 block text-sm font-semibold text-texto">Nueva contraseña</label>
                <input
                    id="perfil-password"
                    type="password"
                    wire:model="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('password') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="perfil-password-confirmation" class="mb-1.5 block text-sm font-semibold text-texto">Confirmar nueva contraseña</label>
                <input
                    id="perfil-password-confirmation"
                    type="password"
                    wire:model="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
            </div>

            <div class="flex justify-end pt-1">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar">
                    <span wire:loading.remove wire:target="guardar">Cambiar contraseña</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-tarjeta>
</div>
