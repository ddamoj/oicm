<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Orquesta la carga de catálogos y datos institucionales de la Fase 1,
     * respetando el orden de dependencias (roles y categorías antes que usuarios y enlaces).
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            DireccionSeeder::class,
            DepartamentoSeeder::class,
            CategoriaDocumentoSeeder::class,
            CategoriaEnlaceSeeder::class,
            NormatividadSeeder::class,
            EnlaceSeeder::class,
            UsuarioSeeder::class,
            NoticiaSeeder::class,
            PaginaInstitucionalSeeder::class,
        ]);
    }
}
