<?php

namespace Database\Seeders;

use App\Models\CategoriaDocumento;
use Illuminate\Database\Seeder;

class CategoriaDocumentoSeeder extends Seeder
{
    /**
     * Catálogo de categorías de documento previsto en el ERS y el brief.
     */
    public function run(): void
    {
        $categorias = [
            ['clave' => 'formatos', 'nombre' => 'Formatos', 'descripcion' => 'Formatos oficiales de uso interno y para las áreas municipales.', 'orden' => 1],
            ['clave' => 'oficios', 'nombre' => 'Oficios', 'descripcion' => 'Oficios y comunicados formales emitidos por las Direcciones del OICM.', 'orden' => 2],
            ['clave' => 'bases-de-datos', 'nombre' => 'Bases de Datos', 'descripcion' => 'Bases de datos e información estructurada de interés público.', 'orden' => 3],
            ['clave' => 'manual-organizacion', 'nombre' => 'Manual de Organización', 'descripcion' => 'Estructura orgánica, funciones, responsabilidades y ámbitos de competencia de cada unidad administrativa del OICM.', 'orden' => 4],
            ['clave' => 'manual-procedimientos', 'nombre' => 'Manual de Procedimientos', 'descripcion' => 'Procedimientos sustantivos del OICM: fundamento legal, objetivo, funciones y formatos básicos.', 'orden' => 5],
        ];

        foreach ($categorias as $categoria) {
            CategoriaDocumento::query()->updateOrCreate(['clave' => $categoria['clave']], $categoria);
        }
    }
}
