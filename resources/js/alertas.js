/**
 * Wrapper único de SweetAlert2 para todo el micrositio del OICM.
 * Centraliza aquí la paleta institucional y el tono de los mensajes para que
 * ninguna vista use alert()/confirm() nativos ni alertas Blade sueltas.
 *
 * Fase 10: SweetAlert2 se importa de forma diferida (solo al mostrar la
 * primera alerta) en vez de al nivel del módulo. La mayoría de las páginas
 * públicas nunca disparan una alerta durante toda la visita, así que
 * cargarla por adelantado en el `app.js` compartido solo penalizaba su LCP.
 */
const COLOR_PRIMARIO = '#265b4d';
const COLOR_ERROR = '#b3261e';

let alertaBasePromise = null;

function obtenerAlertaBase() {
    if (! alertaBasePromise) {
        alertaBasePromise = import('sweetalert2').then(({ default: Swal }) => Swal.mixin({
            confirmButtonColor: COLOR_PRIMARIO,
            cancelButtonColor: '#afafaf',
            buttonsStyling: true,
            customClass: {
                popup: 'font-body',
                confirmButton: 'font-sans',
                cancelButton: 'font-sans',
            },
        }));
    }

    return alertaBasePromise;
}

/**
 * Aviso corto y no bloqueante en la esquina de la pantalla (p. ej. "Guardado").
 */
export async function toast(mensaje, icono = 'success') {
    try {
        const alertaBase = await obtenerAlertaBase();
        alertaBase.fire({
            toast: true,
            position: 'top-end',
            icon: icono,
            title: mensaje,
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true,
        });
    } catch (error) {
        console.error('No fue posible mostrar el toast:', error);
    }
}

/**
 * Diálogo de confirmación antes de una acción irreversible (p. ej. eliminar).
 * Devuelve una promesa que resuelve en true/false según la elección del usuario.
 */
export async function confirmar({
    titulo = '¿Confirmar acción?',
    texto = 'Esta acción no se puede deshacer.',
    textoConfirmar = 'Sí, continuar',
    textoCancelar = 'Cancelar',
} = {}) {
    try {
        const alertaBase = await obtenerAlertaBase();
        const resultado = await alertaBase.fire({
            title: titulo,
            text: texto,
            icon: 'warning',
            iconColor: COLOR_ERROR,
            showCancelButton: true,
            confirmButtonText: textoConfirmar,
            cancelButtonText: textoCancelar,
            reverseButtons: true,
            focusCancel: true,
        });

        return resultado.isConfirmed;
    } catch (error) {
        console.error('No fue posible mostrar el diálogo de confirmación:', error);
        return false;
    }
}

/**
 * Mensaje de éxito centrado, para operaciones importantes (alta, publicación).
 */
export async function exito(mensaje, titulo = 'Listo') {
    try {
        const alertaBase = await obtenerAlertaBase();
        alertaBase.fire({
            title: titulo,
            text: mensaje,
            icon: 'success',
            iconColor: COLOR_PRIMARIO,
            confirmButtonText: 'Entendido',
        });
    } catch (error) {
        console.error('No fue posible mostrar el mensaje de éxito:', error);
    }
}

/**
 * Mensaje de error centrado, para fallas de validación o del servidor.
 */
export async function error(mensaje, titulo = 'Ocurrió un problema') {
    try {
        const alertaBase = await obtenerAlertaBase();
        alertaBase.fire({
            title: titulo,
            text: mensaje,
            icon: 'error',
            iconColor: COLOR_ERROR,
            confirmButtonText: 'Entendido',
        });
    } catch (err) {
        console.error('No fue posible mostrar el mensaje de error:', err);
    }
}

export default { toast, confirmar, exito, error };
