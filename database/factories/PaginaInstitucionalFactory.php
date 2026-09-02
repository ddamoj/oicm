<?php

namespace Database\Factories;

use App\Models\Direccion;
use App\Models\PaginaInstitucional;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaginaInstitucional>
 */
class PaginaInstitucionalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'direccion_id' => Direccion::factory(),
            'slug' => $this->faker->unique()->slug(),
            'titulo' => $this->faker->sentence(4),
            'contenido' => $this->faker->paragraphs(3, true),
            'estatus' => 'publicada',
            'actualizado_por' => null,
        ];
    }

    public function borrador(): static
    {
        return $this->state(fn () => ['estatus' => 'borrador']);
    }
}
