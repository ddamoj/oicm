@props([
    'texto', // Texto visible del globo. Obligatorio.
    'posicion' => 'arriba', // arriba | abajo
])

{{--
    Tooltip accesible para controles solo-icono del panel.

    Decisiones de implementación:
    - El globo se teletransporta al <body> y se posiciona con `fixed`. Las tablas
      del panel viven dentro de `overflow-x-auto`, y ese contenedor recortaría un
      globo posicionado con `absolute` (overflow-x: auto fuerza overflow-y: auto).
    - Se marca `aria-hidden` porque los botones que envuelve ya declaran su
      propósito con `aria-label`; sin esto el lector de pantalla leería el texto
      dos veces.
    - Solo se muestra con foco de teclado (`:focus-visible`), no al tocar en
      móvil, donde un globo sobre el dedo estorba y no aporta.
--}}
<span
    {{ $attributes->merge(['class' => 'inline-flex']) }}
    x-data="{
        visible: false,
        coordenadas: { x: 0, y: 0 },
        posicion: @js($posicion),

        /* Calcula la posición del globo a partir del rectángulo del disparador. */
        ubicar() {
            const disparador = this.$refs.disparador;
            const globo = this.$refs.globo;

            if (! disparador || ! globo) {
                return;
            }

            const areaDisparador = disparador.getBoundingClientRect();
            const areaGlobo = globo.getBoundingClientRect();
            const margen = 8;

            /* Centrado horizontal, recortado para no salirse de la ventana. */
            const centrado = areaDisparador.left + (areaDisparador.width / 2) - (areaGlobo.width / 2);
            const maximo = window.innerWidth - areaGlobo.width - margen;
            this.coordenadas.x = Math.max(margen, Math.min(centrado, maximo));

            /* Arriba por defecto; si no cabe, cae debajo del disparador. */
            const arriba = areaDisparador.top - areaGlobo.height - margen;
            const abajo = areaDisparador.bottom + margen;

            this.coordenadas.y = (this.posicion === 'abajo' || arriba < margen) ? abajo : arriba;
        },

        /* Muestra el globo y lo ubica una vez el DOM lo ha medido. */
        mostrar() {
            this.visible = true;
            this.$nextTick(() => this.ubicar());
        },

        ocultar() {
            this.visible = false;
        },
    }"
    x-on:mouseenter="mostrar()"
    x-on:mouseleave="ocultar()"
    x-on:focusin="$event.target.matches(':focus-visible') && mostrar()"
    x-on:focusout="ocultar()"
    x-on:keydown.escape.window="ocultar()"
    {{-- Al hacer clic el control actúa; el globo ya cumplió su función. --}}
    x-on:click="ocultar()"
    {{-- Reubica el globo si la página se desplaza o cambia de tamaño mientras está visible. --}}
    x-on:scroll.window.passive="visible && ubicar()"
    x-on:resize.window.passive="visible && ubicar()"
>
    {{-- Disparador: el control solo-icono que recibe el tooltip. --}}
    <span x-ref="disparador" class="inline-flex">
        {{ $slot }}
    </span>

    {{-- Globo teletransportado al body para no quedar recortado por la tabla. --}}
    <template x-teleport="body">
        <span
            x-ref="globo"
            x-show="visible"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-bind:style="`left: ${coordenadas.x}px; top: ${coordenadas.y}px;`"
            class="pointer-events-none fixed z-[70] max-w-[16rem] rounded-lg bg-primario-profundo px-2.5 py-1.5
                   font-sans text-xs font-medium leading-snug text-white shadow-lg shadow-primario-profundo/25
                   motion-reduce:transition-none"
            role="presentation"
            aria-hidden="true"
            x-cloak
        >{{ $texto }}</span>
    </template>
</span>
