import { toast, confirmar, exito, error } from './alertas';

// Se exponen globalmente para poder invocarlas desde atributos Alpine
// (x-on:click) y desde scripts embebidos en las vistas Blade.
window.alertas = { toast, confirmar, exito, error };
