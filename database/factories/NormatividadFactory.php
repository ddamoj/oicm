<?php

namespace Database\Factories;

use App\Models\Normatividad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Normatividad>
 */
class NormatividadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ambito' => $this->faker->randomElement(['federal', 'estatal', 'municipal']),
            'titulo' => $this->faker->sentence(5),
            'descripcion' => $this->faker->sentence(),
            'medio_publicacion' => 'Diario Oficial de la Federación',
            'fecha_publicacion' => $this->faker->date(),
            'fecha_ultima_reforma' => $this->faker->date(),
            'documento_url' => null,
            'orden' => $this->faker->numberBetween(0, 10),
            'vigente' => true,
        ];
    }
}
