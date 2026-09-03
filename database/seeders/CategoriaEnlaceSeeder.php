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
            // Añadidas en la Fase 6 (RF-ENL-001/002) para cubrir el catálogo exacto
            // que pide el plan de trabajo, sin remover las categorías ya sembradas
            // en la Fase 1 (decisión: unión de ambos catálogos).
            ['clave' => 'normativa', 'nombre' => 'Normativa', 'orden' => 7],
            ['clave' => 'padron-contratistas', 'nombre' => 'Padrón de contratistas', 'orden' => 8],
            ['clave' => 'dependencias', 'nombre' => 'Dependencias', 'orden' => 9],
        ];

        foreach ($categorias as $categoria) {
            CategoriaEnlace::query()->updateOrCreate(['clave' => $categoria['clave']], $categoria);
        }
    }
}
