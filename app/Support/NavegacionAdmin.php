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
        $iconoUsuarios = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0112.75 0zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM19.5 9.75a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>';

        $comunes = [
            'Panel principal' => ['ruta' => 'admin.panel', 'icono' => $iconoTablero],
            'Documentos' => ['ruta' => 'admin.documentos', 'icono' => $iconoDocumentos],
            'Noticias' => ['ruta' => 'admin.noticias', 'icono' => $iconoNoticias],
            'Enlaces' => ['ruta' => 'admin.enlaces', 'icono' => $iconoEnlaces],
        ];

        // Solo el rol "administrador" gestiona usuarios, roles y catálogos del sistema.
        if ($usuario->tieneRol('administrador')) {
            $comunes['Usuarios'] = ['ruta' => 'admin.usuarios', 'icono' => $iconoUsuarios];
        }

        return $comunes;
    }
}
