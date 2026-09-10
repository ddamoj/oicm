/**
 * Editor de contenido enriquecido para noticias (Fase 5, RF-NOT-001).
 * La barra de herramientas se limita exactamente a lo que
 * App\Support\SaneadorContenido permite guardar en el servidor: cualquier
 * otra opción sería descartada de todos modos al sanear.
 */
const OPCIONES_BARRA = [
    ['bold', 'italic', 'underline'],
    [{ header: 2 }, { header: 3 }],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'link'],
    ['clean'],
];

/**
 * Instancias de Quill vivas, fuera de todo objeto reactivo de Alpine.
 *
 * Motivo (causa raíz del bug de Ctrl+V / Enter): Alpine envuelve el objeto
 * `x-data` en un Proxy reactivo de @vue/reactivity, y ese Proxy es
 * *profundo*: guardar la instancia en `this.quill` hace que cada lectura
 * posterior (`this.quill.setContents(...)`, `this.quill.root`, …) devuelva un
 * Proxy, y que dentro de esas llamadas el `this` de Quill sea también el
 * Proxy. Quill/Parchment se apoyan en comparaciones de identidad estricta
 * —`ScrollBlot.find()` exige `blot.scroll === this`, y `offset()` sube por
 * `parent`— así que los blots creados durante una llamada hecha a través del
 * Proxy quedan con `scroll`/`parent` proxificados dentro del árbol real.
 * A partir de ahí `scroll.find()` devuelve `null` y las rutas internas de
 * pegado (`onCapturePaste`), de Enter y del MutationObserver revientan con
 * «Cannot read properties of null (reading 'offset')».
 *
 * Los nodos del DOM no son proxificables por @vue/reactivity, así que el
 * elemento anfitrión es una clave estable para este WeakMap: la instancia se
 * libera sola cuando el nodo desaparece.
 *
 * @type {WeakMap<Element, { quill: import('quill').default, alCargar: (evento: Event) => void }>}
 */
const editores = new WeakMap();

/**
 * Componente Alpine que monta Quill sobre un contenedor y mantiene
 * sincronizado su HTML con la propiedad Livewire indicada, sin que Livewire
 * vuelva a renderizar el editor en cada tecleo (el contenedor va en
 * `wire:ignore`).
 *
 * Fase 10: Quill (y su CSS) se importan de forma diferida dentro de
 * `iniciar()` en vez de al nivel del módulo, para que Vite los separe en su
 * propio fragmento y las páginas públicas —que jamás muestran este
 * editor— no descarguen su peso en el `app.js` compartido.
 */
export default function editorEnriquecido(valorInicial, propiedad) {
    return {
        // Contenido más reciente recibido por el evento `cargar` mientras
        // Quill todavía se está montando (import diferido); se aplica en
        // cuanto `iniciar` termina de crear la instancia.
        pendiente: null,

        async iniciar() {
            // `this.$el` es un nodo del DOM: Alpine no lo proxifica, de modo
            // que sirve de clave estable para recuperar la instancia cruda.
            const anfitrion = this.$el;

            // Si el componente se reinicializa sobre el mismo nodo (una
            // remorfada de Livewire, por ejemplo), no se construye un segundo
            // Quill sobre el mismo contenedor: se reutiliza el existente.
            if (editores.has(anfitrion)) {
                return;
            }

            const alCargar = (evento) => this.cargar(evento?.detail?.contenido ?? '');
            window.addEventListener(`editor:${propiedad}:cargar`, alCargar);

            // Reserva el hueco antes del import diferido para que dos
            // ejecuciones concurrentes de `iniciar()` no creen dos instancias.
            editores.set(anfitrion, { quill: null, alCargar });

            try {
                const [{ default: Quill }] = await Promise.all([
                    import('quill'),
                    import('quill/dist/quill.snow.css'),
                ]);

                const quill = new Quill(this.$refs.editor, {
                    theme: 'snow',
                    modules: { toolbar: OPCIONES_BARRA },
                    placeholder: 'Escribe el contenido…',
                });

                editores.set(anfitrion, { quill, alCargar });

                this.cargar(this.pendiente ?? valorInicial);

                // El manejador usa la referencia cruda del closure, nunca
                // `this.quill`: así ninguna llamada a Quill pasa por el Proxy.
                quill.on('text-change', () => {
                    try {
                        const html = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
                        this.$wire.set(propiedad, html, false);
                    } catch (error) {
                        console.error('No fue posible sincronizar el contenido del editor.', error);
                    }
                });
            } catch (error) {
                // Si el montaje falla, se limpia el registro para permitir un
                // reintento en una reinicialización posterior.
                window.removeEventListener(`editor:${propiedad}:cargar`, alCargar);
                editores.delete(anfitrion);
                console.error('No fue posible montar el editor de contenido enriquecido.', error);
            }
        },

        /**
         * Alpine invoca `destroy()` al desmontar el componente: se retira el
         * listener de `window` para que una reinicialización no acumule
         * manejadores apuntando a instancias ya muertas.
         */
        destroy() {
            const registro = editores.get(this.$el);

            if (! registro) {
                return;
            }

            window.removeEventListener(`editor:${propiedad}:cargar`, registro.alCargar);
            editores.delete(this.$el);
        },

        cargar(html) {
            const quill = editores.get(this.$el)?.quill ?? null;

            if (! quill) {
                this.pendiente = html;

                return;
            }

            try {
                // No usar clipboard.dangerouslyPasteHTML(html): con un solo
                // argumento, ese método hace internamente
                // setSelection(0, SILENT) después de fijar el contenido, y esa
                // llamada lanza una excepción al reutilizar el editor para un
                // segundo registro. `convert` + `setContents` insertan el mismo
                // contenido sin esa llamada.
                const delta = quill.clipboard.convert({ html: html || '', text: '' });
                quill.setContents(delta, 'silent');
            } catch (error) {
                console.error('No fue posible cargar el contenido en el editor.', error);
            }
        },
    };
}
