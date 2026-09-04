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
        quill: null,

        async iniciar() {
            const [{ default: Quill }] = await Promise.all([
                import('quill'),
                import('quill/dist/quill.snow.css'),
            ]);

            this.quill = new Quill(this.$refs.editor, {
                theme: 'snow',
                modules: { toolbar: OPCIONES_BARRA },
                placeholder: 'Escribe el contenido de la noticia…',
            });

            if (valorInicial) {
                this.quill.clipboard.dangerouslyPasteHTML(valorInicial);
            }

            this.quill.on('text-change', () => {
                const html = this.quill.getText().trim() === '' ? '' : this.quill.root.innerHTML;
                this.$wire.set(propiedad, html, false);
            });
        },
    };
}
