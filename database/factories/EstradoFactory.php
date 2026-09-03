<?php

namespace Database\Factories;

use App\Models\Estrado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estrado>
 */
class EstradoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero' => $this->faker->unique()->numberBetween(1, 10000),
            'expediente' => 'OICM/DRACS/'.$this->faker->numberBetween(1, 999).'/2026',
            'asunto' => $this->faker->sentence(6),
            'fecha_publicacion' => now(),
            'archivo_ruta' => 'estrados/2026/'.$this->faker->uuid().'.pdf',
            'archivo_nombre' => 'notificacion.pdf',
            'archivo_extension' => 'pdf',
            'archivo_mime' => 'application/pdf',
            'archivo_tamano' => $this->faker->numberBetween(1024, 500000),
            'archivo_hash' => hash('sha256', $this->faker->uuid()),
            'datos_testados' => true,
            'publicado_por' => null,
            'activo' => true,
        ];
    }
}
