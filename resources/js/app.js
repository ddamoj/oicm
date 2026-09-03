import { toast, confirmar, exito, error } from './alertas';
import editorEnriquecido from './editor';

// Se exponen globalmente para poder invocarlas desde atributos Alpine
// (x-on:click) y desde scripts embebidos en las vistas Blade.
window.alertas = { toast, confirmar, exito, error };

// Registra el editor de noticias como componente Alpine reutilizable
// (x-data="editorEnriquecido(...)") en cuanto Livewire inicializa Alpine.
document.addEventListener('alpine:init', () => {
    window.Alpine.data('editorEnriquecido', editorEnriquecido);
});

// Puente único entre acciones Livewire y SweetAlert2 (Fase 3): cualquier
// componente administrativo notifica al usuario despachando estos eventos
// desde PHP (dispatch('mostrar-exito', ...)). Vivía como <script> inline en
// components/layouts/admin.blade.php; se mueve aquí en la Fase 9 para que la
// Content-Security-Policy no necesite 'unsafe-inline' en script-src.
window.addEventListener('mostrar-exito', (evento) => window.alertas.exito(evento.detail.mensaje));
window.addEventListener('mostrar-error', (evento) => window.alertas.error(evento.detail.mensaje));

// Mensajería del layout de autenticación (Fase 3) vía SweetAlert2: el layout
// deja el mensaje de error/estado en atributos data-* de un nodo oculto (no
// puede ser un <script> inline dinámico bajo CSP estricto) y aquí se lee y
// se despacha con el mismo wrapper que usa el resto del sitio.
document.addEventListener('DOMContentLoaded', () => {
    const nodo = document.getElementById('mensajes-autenticacion');
    if (! nodo) {
        return;
    }

    if (nodo.dataset.error) {
        window.alertas.error(nodo.dataset.error, 'Revisa los datos');
    }

    if (nodo.dataset.status) {
        window.alertas.exito(nodo.dataset.status);
    }
});
