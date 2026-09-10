<?php

namespace Database\Factories;

use App\Models\VisitaPagina;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitaPagina>
 */
class VisitaPaginaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ruta' => '/'.$this->faker->randomElement(['', 'noticias', 'documentos', 'quienes-somos', 'normatividad']),
            'ip_huella' => hash('sha256', $this->faker->unique()->ipv4()),
            'dispositivo' => $this->faker->randomElement(['escritorio', 'movil', 'tableta']),
            'visitada_en' => now(),
        ];
    }

    /** Visita ocurrida hace `$dias` días, para poblar las series del tablero. */
    public function haceDias(int $dias): static
    {
        return $this->state(fn (): array => ['visitada_en' => now()->subDays($dias)]);
    }
}
