<?php

namespace Database\Factories;

use App\Models\CategoriaDocumento;
use App\Models\Documento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Documento>
 */
class DocumentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'categoria_documento_id' => CategoriaDocumento::factory(),
            'direccion_id' => null,
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->sentence(),
            'ruta_archivo' => 'documentos/'.$this->faker->uuid().'.pdf',
            'nombre_original' => $this->faker->word().'.pdf',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamano_bytes' => $this->faker->numberBetween(1024, 5_000_000),
            'contador_descargas' => 0,
            'publicado' => true,
            'subido_por' => null,
        ];
    }
}
