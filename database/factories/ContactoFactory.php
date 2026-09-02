<?php

namespace Database\Factories;

use App\Models\Contacto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contacto>
 */
class ContactoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'direccion_id' => null,
            'nombre_area' => $this->faker->words(3, true),
            'domicilio' => $this->faker->address(),
            'telefono' => $this->faker->phoneNumber(),
            'correo' => $this->faker->companyEmail(),
            'horario' => 'Lunes a viernes, 9:00 a 16:00 horas',
        ];
    }
}
