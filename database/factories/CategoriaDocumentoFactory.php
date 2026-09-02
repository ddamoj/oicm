<?php

namespace Database\Factories;

use App\Models\CategoriaDocumento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoriaDocumento>
 */
class CategoriaDocumentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clave' => $this->faker->unique()->word(),
            'nombre' => $this->faker->words(2, true),
            'descripcion' => $this->faker->sentence(),
            'orden' => $this->faker->numberBetween(0, 10),
        ];
    }
}
