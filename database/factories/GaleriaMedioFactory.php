<?php

namespace Database\Factories;

use App\Models\Galeria;
use App\Models\GaleriaMedio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GaleriaMedio>
 */
class GaleriaMedioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'galeria_id' => Galeria::factory(),
            'tipo' => 'foto',
            'ruta_archivo' => 'galerias/'.$this->faker->uuid().'.jpg',
            'descripcion_alt' => $this->faker->sentence(),
            'orden' => $this->faker->numberBetween(0, 10),
        ];
    }
}
