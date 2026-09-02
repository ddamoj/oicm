<?php

namespace Database\Factories;

use App\Models\Noticia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Noticia>
 */
class NoticiaFactory extends Factory
{
    public function definition(): array
    {
        $titulo = $this->faker->unique()->sentence(6);

        return [
            'titulo' => $titulo,
            'slug' => str($titulo)->slug(),
            'contenido' => $this->faker->paragraphs(4, true),
            'imagen_portada' => null,
            'estatus' => 'publicada',
            'publicado_en' => now(),
            'autor_id' => null,
        ];
    }

    public function borrador(): static
    {
        return $this->state(fn () => ['estatus' => 'borrador', 'publicado_en' => null]);
    }
}
