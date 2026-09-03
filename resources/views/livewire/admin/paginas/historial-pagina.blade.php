<div>
    <x-modal nombre="historial-pagina" :titulo="'Historial: '.$tituloPagina" maxAncho="lg">
        @if ($versiones->isEmpty())
            <x-vacio titulo="Sin versiones anteriores" descripcion="Esta página aún no tiene ediciones archivadas." />
        @else
            <ul class="space-y-3">
                @foreach ($versiones as $version)
                    <li wire:key="version-{{ $version->id }}" class="flex items-center justify-between gap-4 rounded-lg border border-borde p-4">
                        <div>
                            <p class="text-sm font-semibold text-texto">Versión {{ $version->numero_version }} — {{ $version->titulo }}</p>
                            <p class="text-xs text-texto-secundario">
                                Guardada el {{ $version->created_at->format('d/m/Y H:i') }}
                                @if ($version->actualizadoPor)
                                    por {{ $version->actualizadoPor->name }}
                                @endif
                            </p>
                        </div>

                        <div x-data="{
                            async confirmarYRestaurar() {
                                const confirmado = await window.alertas.confirmar({
                                    titulo: '¿Restaurar la versión {{ $version->numero_version }}?',
                                    texto: 'El contenido vigente se archivará como una nueva versión antes de restaurar esta.',
                                });
                                if (confirmado) { $wire.restaurar({{ $version->id }}); }
                            },
                        }">
                            <x-boton x-on:click="confirmarYRestaurar" variante="secundario" tamano="sm">Restaurar</x-boton>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-modal>
</div>
