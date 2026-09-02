<?php

namespace Database\Factories;

use App\Models\Direccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Direccion>
 */
class DireccionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clave' => $this->faker->unique()->slug(2),
            'nombre' => 'Dirección de '.$this->faker->words(3, true),
            'siglas' => strtoupper($this->faker->lexify('???')),
            'descripcion' => $this->faker->sentence(),
            'orden' => $this->faker->numberBetween(0, 10),
            'activa' => true,
        ];
    }
}
