<?php

namespace Database\Factories;

use App\Models\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rol>
 */
class RolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clave' => $this->faker->unique()->word(),
            'nombre' => $this->faker->words(2, true),
            'descripcion' => $this->faker->sentence(),
        ];
    }
}
