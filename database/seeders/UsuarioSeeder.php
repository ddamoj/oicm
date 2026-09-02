<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Crea usuarios de prueba para cada rol administrativo, útiles para el
     * desarrollo local y las pruebas manuales de la Fase 3 (autenticación).
     */
    public function run(): void
    {
        $rolAdministrador = Rol::query()->where('clave', 'administrador')->first();
        $rolAdministradorContenido = Rol::query()->where('clave', 'administrador_contenido')->first();

        User::query()->updateOrCreate(
            ['email' => 'administrador@oicm.oaxacadejuarez.gob.mx'],
            [
                'name' => 'Administrador OICM',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'rol_id' => $rolAdministrador?->id,
                'activo' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'contenido@oicm.oaxacadejuarez.gob.mx'],
            [
                'name' => 'Administrador de Contenido OICM',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'rol_id' => $rolAdministradorContenido?->id,
                'activo' => true,
            ]
        );
    }
}
