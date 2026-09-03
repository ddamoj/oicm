<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Direccion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Departamento>
 */
class DepartamentoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'direccion_id' => Direccion::factory(),
            'nombre' => 'Departamento de '.$this->faker->words(2, true),
            'siglas' => null,
            'descripcion' => $this->faker->sentence(),
            'orden' => $this->faker->numberBetween(0, 10),
            'activo' => true,
        ];
    }
}
