<?php

namespace Database\Factories;

use App\Models\CategoriaEnlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CategoriaEnlace>
 */
class CategoriaEnlaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clave' => $this->faker->unique()->word(),
            'nombre' => $this->faker->words(2, true),
            'orden' => $this->faker->numberBetween(0, 10),
        ];
    }
}
