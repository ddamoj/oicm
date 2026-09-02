<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Crea el catálogo fijo de roles administrativos del sistema (ERS §2).
     * El rol "visitante" no se registra porque no requiere autenticación.
     */
    public function run(): void
    {
        $roles = [
            [
                'clave' => 'administrador',
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total: usuarios, roles, catálogos, parámetros y todos los módulos de contenido.',
            ],
            [
                'clave' => 'administrador_contenido',
                'nombre' => 'Administrador de Contenido',
                'descripcion' => 'Gestiona noticias, avisos, documentos, enlaces e información institucional. Sin acceso a usuarios, roles ni configuración global.',
            ],
        ];

        foreach ($roles as $rol) {
            Rol::query()->updateOrCreate(['clave' => $rol['clave']], $rol);
        }
    }
}
