<?php

namespace Database\Factories;

use App\Models\PaginaInstitucional;
use App\Models\PaginaInstitucionalVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaginaInstitucionalVersion>
 */
class PaginaInstitucionalVersionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pagina_institucional_id' => PaginaInstitucional::factory(),
            'numero_version' => 1,
            'titulo' => $this->faker->sentence(4),
            'contenido' => $this->faker->paragraphs(3, true),
            'actualizado_por' => null,
        ];
    }
}
