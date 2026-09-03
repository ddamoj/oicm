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
