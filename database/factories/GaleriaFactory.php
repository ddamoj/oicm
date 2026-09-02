<?php

namespace Database\Factories;

use App\Models\Galeria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Galeria>
 */
class GaleriaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(3),
            'descripcion' => $this->faker->sentence(),
            'fecha_evento' => $this->faker->date(),
            'publicada' => true,
        ];
    }
}
