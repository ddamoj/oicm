<?php

namespace Database\Factories;

use App\Models\Documento;
use App\Models\DocumentoVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentoVersion>
 */
class DocumentoVersionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'documento_id' => Documento::factory(),
            'numero_version' => 1,
            'ruta_archivo' => 'documentos/versiones/'.$this->faker->uuid().'.pdf',
            'nombre_original' => $this->faker->word().'.pdf',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'tamano_bytes' => $this->faker->numberBetween(1024, 5_000_000),
            'reemplazado_por' => null,
        ];
    }
}
