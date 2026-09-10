<div>
    <x-migas :items="['Estadísticas' => null]" raiz="admin.panel" etiqueta-raiz="Panel principal" class="mb-6" />

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-texto">Estadísticas de visitas</h1>
            <p class="mt-1 text-sm text-texto-secundario">Uso del micrositio público, medido sin cookies ni servicios de terceros.</p>
        </div>

        <div>
            <label for="periodo-estadisticas" class="mb-1.5 block text-sm font-semibold text-texto">Periodo</label>
            <select
                id="periodo-estadisticas"
                wire:model.live="periodo"
                class="rounded-lg border border-borde bg-superficie px-4 py-2.5 text-sm text-texto focus:border-primario focus:outline-none focus:ring-2 focus:ring-acento-oscuro"
            >
                @foreach (\App\Livewire\Admin\Estadisticas\TableroVisitas::PERIODOS as $valor => $etiqueta)
                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Indicadores de cabecera --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-tarjeta acento="primario">
            <p class="text-sm font-semibold text-texto-secundario">Visitas hoy</p>
            <p class="mt-2 text-4xl font-bold tracking-tight text-primario">{{ number_format($this->indicadores['hoy']) }}</p>
            <p class="mt-1 text-xs text-texto-secundario">{{ number_format($this->indicadores['unicosHoy']) }} visitantes distintos</p>
        </x-tarjeta>

        <x-tarjeta>
            <p class="text-sm font-semibold text-texto-secundario">Esta semana</p>
            <p class="mt-2 text-4xl font-bold tracking-tight text-primario">{{ number_format($this->indicadores['semana']) }}</p>
            <p class="mt-1 text-xs text-texto-secundario">de lunes a domingo</p>
        </x-tarjeta>

        <x-tarjeta>
            <p class="text-sm font-semibold text-texto-secundario">Este mes</p>
            <p class="mt-2 text-4xl font-bold tracking-tight text-primario">{{ number_format($this->indicadores['mes']) }}</p>
            <p class="mt-1 text-xs text-texto-secundario">{{ number_format($this->indicadores['unicosMes']) }} visitantes distintos</p>
        </x-tarjeta>

        <x-tarjeta acento="acento">
            <p class="text-sm font-semibold text-texto-secundario">Visitas totales</p>
            <p class="mt-2 text-4xl font-bold tracking-tight text-primario">{{ number_format($this->indicadores['total']) }}</p>
            <p class="mt-1 text-xs text-texto-secundario">{{ number_format($this->indicadores['unicosTotal']) }} visitantes distintos</p>
        </x-tarjeta>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Serie diaria. Barras en CSS: el proyecto evita librerías de
             gráficas (mismo criterio que el organigrama de la Fase 7) y la
             CSP de la Fase 9 no admite scripts de CDN. --}}
        <div class="lg:col-span-2">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Visitas por día</h2>

            <x-tarjeta>
                @if (array_sum(array_column($this->porDia, 'total')) === 0)
                    <p class="py-10 text-center text-sm text-texto-secundario">
                        Todavía no hay visitas registradas en este periodo.
                    </p>
                @else
                    <div class="flex h-56 items-stretch gap-1 overflow-x-auto" role="img"
                         aria-label="Gráfica de visitas por día durante los últimos {{ count($this->porDia) }} días">
                        @foreach ($this->porDia as $dia)
                            <div class="group flex h-full min-w-[8px] flex-1 flex-col items-center justify-end gap-1">
                                <span class="text-[10px] font-semibold text-texto-secundario opacity-0 transition-opacity group-hover:opacity-100">
                                    {{ $dia['total'] }}
                                </span>
                                <div
                                    class="w-full rounded-t bg-acento-oscuro/70 transition-all duration-300 ease-institucional group-hover:bg-primario"
                                    style="height: {{ max(round($dia['total'] / $this->maximoDiario * 100), $dia['total'] > 0 ? 2 : 0) }}%"
                                    title="{{ $dia['fecha'] }}: {{ $dia['total'] }} {{ $dia['total'] === 1 ? 'visita' : 'visitas' }}"
                                ></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-2 flex justify-between text-xs text-texto-secundario">
                        <span>{{ $this->porDia[0]['etiqueta'] }}</span>
                        <span>{{ $this->porDia[count($this->porDia) - 1]['etiqueta'] }}</span>
                    </div>

                    {{-- Alternativa accesible: la gráfica es decorativa para
                         quien usa lector de pantalla, la tabla es el dato. --}}
                    <details class="mt-4">
                        <summary class="cursor-pointer text-xs font-semibold text-primario">Ver los datos en tabla</summary>
                        <table class="mt-3 w-full text-left text-xs">
                            <thead class="text-texto-secundario">
                                <tr><th class="py-1 font-semibold">Día</th><th class="py-1 font-semibold">Visitas</th></tr>
                            </thead>
                            <tbody class="divide-y divide-borde">
                                @foreach ($this->porDia as $dia)
                                    <tr><td class="py-1 text-texto">{{ $dia['fecha'] }}</td><td class="py-1 text-texto-secundario">{{ $dia['total'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </details>
                @endif
            </x-tarjeta>
        </div>

        {{-- Dispositivos --}}
        <div>
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Dispositivos</h2>

            <x-tarjeta>
                @foreach ($this->porDispositivo as $dispositivo)
                    <div class="mb-4 last:mb-0">
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-semibold text-texto">{{ $dispositivo['etiqueta'] }}</span>
                            <span class="text-xs text-texto-secundario">
                                {{ number_format($dispositivo['total']) }} · {{ $dispositivo['porcentaje'] }}%
                            </span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gris-claro">
                            <div
                                class="h-full rounded-full bg-primario transition-all duration-500 ease-institucional"
                                style="width: {{ $dispositivo['porcentaje'] }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach

                @if (array_sum(array_column($this->porDispositivo, 'total')) === 0)
                    <p class="pt-2 text-center text-xs text-texto-secundario">Sin datos en el periodo seleccionado.</p>
                @endif
            </x-tarjeta>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        {{-- Páginas más visitadas --}}
        <div>
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Páginas más visitadas</h2>

            @if (empty($this->topPaginas))
                <x-vacio titulo="Sin datos en el periodo" descripcion="Las visitas se registran conforme la ciudadanía navega el micrositio." />
            @else
                <x-tarjeta class="!p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-borde text-xs uppercase tracking-wide text-texto-secundario">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Ruta</th>
                                    <th class="px-5 py-3 font-semibold">Visitas</th>
                                    <th class="px-5 py-3 font-semibold">Distintos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-borde">
                                @foreach ($this->topPaginas as $pagina)
                                    <tr wire:key="ruta-{{ md5($pagina['ruta']) }}">
                                        <td class="px-5 py-3 font-mono text-xs text-texto">{{ $pagina['ruta'] }}</td>
                                        <td class="px-5 py-3"><x-badge variante="primario">{{ number_format($pagina['total']) }}</x-badge></td>
                                        <td class="px-5 py-3 text-xs text-texto-secundario">{{ number_format($pagina['unicos']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-tarjeta>
            @endif
        </div>

        {{-- Tendencia de los últimos 12 meses --}}
        <div>
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-texto-secundario">Tendencia (12 meses)</h2>

            <x-tarjeta>
                @if (array_sum(array_column($this->porMes, 'total')) === 0)
                    <p class="py-10 text-center text-sm text-texto-secundario">Todavía no hay histórico suficiente.</p>
                @else
                    <div class="flex h-40 items-stretch gap-2" role="img" aria-label="Gráfica de visitas por mes durante los últimos 12 meses">
                        @foreach ($this->porMes as $mes)
                            <div class="group flex h-full flex-1 flex-col items-center justify-end gap-1">
                                <span class="text-[10px] font-semibold text-texto-secundario opacity-0 transition-opacity group-hover:opacity-100">
                                    {{ $mes['total'] }}
                                </span>
                                <div
                                    class="w-full rounded-t bg-primario/70 transition-all duration-300 ease-institucional group-hover:bg-primario"
                                    style="height: {{ max(round($mes['total'] / $this->maximoMensual * 100), $mes['total'] > 0 ? 2 : 0) }}%"
                                    title="{{ $mes['mes'] }}: {{ $mes['total'] }} {{ $mes['total'] === 1 ? 'visita' : 'visitas' }}"
                                ></div>
                                <span class="text-[10px] text-texto-secundario">{{ Str::before($mes['mes'], ' ') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-tarjeta>
        </div>
    </div>

    {{-- Nota de privacidad: el aviso de la Fase 9 declara esta medición. --}}
    <div class="mt-8 flex items-start gap-3 rounded-xl border border-exito/20 bg-exito-suave/40 p-5">
        <svg class="mt-0.5 size-5 shrink-0 text-exito" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 1.5a.75.75 0 01.3.062l6 2.5A.75.75 0 0116.75 4.75v4.5c0 4.02-2.62 7.53-6.5 8.72a.75.75 0 01-.5 0C5.87 16.78 3.25 13.27 3.25 9.25v-4.5a.75.75 0 01.45-.688l6-2.5A.75.75 0 0110 1.5zm3.03 6.28a.75.75 0 10-1.06-1.06L9 9.69 7.78 8.47a.75.75 0 00-1.06 1.06l1.75 1.75a.75.75 0 001.06 0l3.5-3.5z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm text-texto">
            No se registran datos personales ni se identifica a ninguna persona. De cada visita solo se guardan la ruta, el
            tipo de dispositivo y una huella irreversible de la dirección IP (HMAC-SHA256), que no permite reconstruirla. Se
            descartan los rastreadores automáticos y no se usan cookies ni servicios de analítica de terceros.
        </p>
    </div>
</div>
