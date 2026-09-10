<?php

namespace App\Support;

use App\Models\User;

/**
 * Navegación lateral del panel administrativo, filtrada por rol.
 * El rol "administrador" ve todo; "administrador_contenido" no ve usuarios ni catálogos.
 */
class NavegacionAdmin
{
    /**
     * @return array<string, array{ruta: string, icono: string}>
     */
    public static function enlaces(?User $usuario): array
    {
        // Sin usuario autenticado (no debería ocurrir tras el middleware de auth),
        // se devuelve un menú vacío por seguridad ante fallos de sesión.
        if (! $usuario) {
            return [];
        }

        $iconoTablero = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75A2.25 2.25 0 016 4.5h2.25a2.25 2.25 0 012.25 2.25V9A2.25 2.25 0 018.25 11.25H6A2.25 2.25 0 013.75 9V6.75zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V9A2.25 2.25 0 0118 11.25h-2.25A2.25 2.25 0 0113.5 9V6.75zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>';
        $iconoDocumentos = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H8.25m5.231 13.481L15 14.25m0 0l-2.25 2.25M15 14.25v6M6.75 21h10.5a2.25 2.25 0 002.25-2.25V9.75L15 3H6.75a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006.75 21z" /></svg>';
        $iconoNoticias = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H8.25M9 12h6m-6 3.75h6M5.25 21h13.5A2.25 2.25 0 0021 18.75V7.5l-6-5.25H5.25A2.25 2.25 0 003 4.5v14.25A2.25 2.25 0 005.25 21z" /></svg>';
        $iconoEnlaces = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>';
        $iconoPaginas = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>';
        $iconoNormatividad = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0-17.25c-1.472 0-2.882.265-4.185.75M12 3c1.472 0 2.882.265 4.185.75M18.75 20.25V7.5a48.667 48.667 0 00-3-.478m0 0a48.474 48.474 0 00-6 0m6 0v.243a.75.75 0 01-.375.65 5.982 5.982 0 01-2.625.622 5.982 5.982 0 01-2.625-.622.75.75 0 01-.375-.65V7.023m9 12.977a48.474 48.474 0 01-9 0" /></svg>';
        $iconoEstrados = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>';
        $iconoGalerias = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg>';
        $iconoContactos = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.379 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" /></svg>';
        $iconoDirecciones = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>';
        $iconoEstadisticas = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>';
        $iconoUsuarios = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0112.75 0zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM19.5 9.75a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>';
        $iconoBitacora = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 3.75h6M9 4.5v.75m6-.75v.75M6.75 6h10.5A2.25 2.25 0 0119.5 8.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V8.25A2.25 2.25 0 016.75 6z" /></svg>';

        $comunes = [
            'Panel principal' => ['ruta' => 'admin.panel', 'icono' => $iconoTablero],
            'Documentos' => ['ruta' => 'admin.documentos', 'icono' => $iconoDocumentos],
            'Noticias' => ['ruta' => 'admin.noticias', 'icono' => $iconoNoticias],
            'Enlaces' => ['ruta' => 'admin.enlaces', 'icono' => $iconoEnlaces],
            'Páginas institucionales' => ['ruta' => 'admin.paginas', 'icono' => $iconoPaginas],
            'Normatividad' => ['ruta' => 'admin.normatividad', 'icono' => $iconoNormatividad],
            'Estrados digitales' => ['ruta' => 'admin.estrados', 'icono' => $iconoEstrados],
            'Galerías' => ['ruta' => 'admin.galerias', 'icono' => $iconoGalerias],
            'Contacto' => ['ruta' => 'admin.contactos', 'icono' => $iconoContactos],
            'Estadísticas' => ['ruta' => 'admin.estadisticas', 'icono' => $iconoEstadisticas],
        ];

        // Solo el rol "administrador" gestiona usuarios, roles, catálogos y la bitácora del sistema.
        if ($usuario->tieneRol('administrador')) {
            $comunes['Direcciones'] = ['ruta' => 'admin.direcciones', 'icono' => $iconoDirecciones];
            $comunes['Usuarios'] = ['ruta' => 'admin.usuarios', 'icono' => $iconoUsuarios];
            $comunes['Bitácora'] = ['ruta' => 'admin.bitacora', 'icono' => $iconoBitacora];
        }

        return $comunes;
    }
}
