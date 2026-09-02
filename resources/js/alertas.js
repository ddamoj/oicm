import Swal from 'sweetalert2';

/**
 * Wrapper único de SweetAlert2 para todo el micrositio del OICM.
 * Centraliza aquí la paleta institucional y el tono de los mensajes para que
 * ninguna vista use alert()/confirm() nativos ni alertas Blade sueltas.
 */
const COLOR_PRIMARIO = '#265b4d';
const COLOR_ACENTO = '#cdde00';
const COLOR_ERROR = '#b3261e';

const alertaBase = Swal.mixin({
    confirmButtonColor: COLOR_PRIMARIO,
    cancelButtonColor: '#afafaf',
    buttonsStyling: true,
    customClass: {
        popup: 'font-body',
        confirmButton: 'font-sans',
        cancelButton: 'font-sans',
    },
});

/**
 * Aviso corto y no bloqueante en la esquina de la pantalla (p. ej. "Guardado").
 */
export function toast(mensaje, icono = 'success') {
    try {
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
export function confirmar({
    titulo = '¿Confirmar acción?',
    texto = 'Esta acción no se puede deshacer.',
    textoConfirmar = 'Sí, continuar',
    textoCancelar = 'Cancelar',
} = {}) {
    return alertaBase
        .fire({
            title: titulo,
            text: texto,
            icon: 'warning',
            iconColor: COLOR_ERROR,
            showCancelButton: true,
            confirmButtonText: textoConfirmar,
            cancelButtonText: textoCancelar,
            reverseButtons: true,
            focusCancel: true,
        })
        .then((resultado) => resultado.isConfirmed)
        .catch((error) => {
            console.error('No fue posible mostrar el diálogo de confirmación:', error);
            return false;
        });
}

/**
 * Mensaje de éxito centrado, para operaciones importantes (alta, publicación).
 */
export function exito(mensaje, titulo = 'Listo') {
    try {
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
export function error(mensaje, titulo = 'Ocurrió un problema') {
    try {
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
