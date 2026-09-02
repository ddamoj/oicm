<?php

namespace Database\Factories;

use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enlace>
 */
class EnlaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'categoria_enlace_id' => CategoriaEnlace::factory(),
            'nombre' => $this->faker->company(),
            'descripcion' => $this->faker->sentence(),
            'url' => $this->faker->url(),
            'orden' => $this->faker->numberBetween(0, 10),
            'activo' => true,
        ];
    }
}
