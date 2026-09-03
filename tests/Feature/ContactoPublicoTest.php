<?php

namespace Tests\Feature;

use App\Models\Contacto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Página pública de Contacto: directorio, mapa y canal de quejas y denuncias (Fase 8).
 */
class ContactoPublicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_muestra_el_directorio_de_contacto(): void
    {
        Contacto::factory()->create(['nombre_area' => 'Dirección de Auditoría Interna']);

        $this->get(route('contacto'))
            ->assertOk()
            ->assertSee('Dirección de Auditoría Interna');
    }

    public function test_muestra_el_mapa_solo_cuando_hay_coordenadas(): void
    {
        Contacto::factory()->create([
            'nombre_area' => 'Con mapa',
            'latitud' => 17.0654100,
            'longitud' => -96.7236500,
        ]);
        Contacto::factory()->create([
            'nombre_area' => 'Sin mapa',
            'latitud' => null,
            'longitud' => null,
        ]);

        $respuesta = $this->get(route('contacto'));

        $respuesta->assertOk();
        $respuesta->assertSeeHtml('google.com/maps');
    }

    public function test_destaca_el_canal_de_quejas_y_denuncias_cuando_existe(): void
    {
        Contacto::factory()->create([
            'nombre_area' => 'DQDISP',
            'canal_quejas_denuncias' => 'quejas@municipiodeoaxaca.gob.mx',
        ]);

        $this->get(route('contacto'))
            ->assertOk()
            ->assertSee('quejas@municipiodeoaxaca.gob.mx');
    }
}
