<?php

namespace Database\Seeders;

use App\Models\CategoriaEnlace;
use Illuminate\Database\Seeder;

class CategoriaEnlaceSeeder extends Seeder
{
    /**
     * Catálogo de categorías de enlaces externos (RF-ENL-001/002).
     */
    public function run(): void
    {
        $categorias = [
            ['clave' => 'declaraciones', 'nombre' => 'Declaraciones patrimoniales', 'orden' => 1],
            ['clave' => 'control-interno', 'nombre' => 'Evaluación del control interno', 'orden' => 2],
            ['clave' => 'entrega-recepcion', 'nombre' => 'Entrega-recepción', 'orden' => 3],
            ['clave' => 'transparencia', 'nombre' => 'Transparencia y rendición de cuentas', 'orden' => 4],
            ['clave' => 'organismos-fiscalizadores', 'nombre' => 'Organismos fiscalizadores y anticorrupción', 'orden' => 5],
            ['clave' => 'centros-analisis', 'nombre' => 'Centros de análisis e investigación', 'orden' => 6],
        ];

        foreach ($categorias as $categoria) {
            CategoriaEnlace::query()->updateOrCreate(['clave' => $categoria['clave']], $categoria);
        }
    }
}
