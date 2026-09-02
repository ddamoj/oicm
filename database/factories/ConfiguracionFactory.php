<?php

namespace Database\Factories;

use App\Models\Configuracion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Configuracion>
 */
class ConfiguracionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clave' => $this->faker->unique()->word(),
            'valor' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
        ];
    }
}
