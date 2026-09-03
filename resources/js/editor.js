import Quill from 'quill';
import 'quill/dist/quill.snow.css';

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
 */
export default function editorEnriquecido(valorInicial, propiedad) {
    return {
        quill: null,

        iniciar() {
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
