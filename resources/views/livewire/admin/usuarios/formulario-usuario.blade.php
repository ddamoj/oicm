<div>
    <x-modal nombre="formulario-usuario" :titulo="$usuarioId ? 'Editar usuario' : 'Nuevo usuario'" maxAncho="md">
        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label for="form-usuario-name" class="mb-1.5 block text-sm font-semibold text-texto">Nombre completo</label>
                <input
                    id="form-usuario-name"
                    type="text"
                    wire:model="name"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('name') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-usuario-email" class="mb-1.5 block text-sm font-semibold text-texto">Correo institucional</label>
                <input
                    id="form-usuario-email"
                    type="email"
                    wire:model="email"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('email') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-usuario-rol" class="mb-1.5 block text-sm font-semibold text-texto">Rol</label>
                <select
                    id="form-usuario-rol"
                    wire:model="rolId"
                    required
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                    <option value="">Selecciona un rol…</option>
                    @foreach ($this->roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                    @endforeach
                </select>
                @error('rolId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="form-usuario-password" class="mb-1.5 block text-sm font-semibold text-texto">
                    Contraseña
                    @if ($usuarioId)
                        <span class="font-normal text-texto-secundario">(déjala en blanco para no cambiarla)</span>
                    @endif
                </label>
                <input
                    id="form-usuario-password"
                    type="password"
                    wire:model="password"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
                @error('password') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-texto-secundario">Mínimo 10 caracteres, con mayúsculas, minúsculas, números y símbolos.</p>
            </div>

            <div>
                <label for="form-usuario-password-confirmation" class="mb-1.5 block text-sm font-semibold text-texto">Confirmar contraseña</label>
                <input
                    id="form-usuario-password-confirmation"
                    type="password"
                    wire:model="password_confirmation"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
                >
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-boton tipo="submit" variante="primario" wire:loading.attr="disabled" wire:target="guardar">
                    <span wire:loading.remove wire:target="guardar">Guardar</span>
                    <span wire:loading wire:target="guardar">Guardando…</span>
                </x-boton>
            </div>
        </form>
    </x-modal>
</div>
