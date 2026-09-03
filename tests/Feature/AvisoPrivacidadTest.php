<?php

namespace Tests\Feature;

use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Aviso de privacidad (Fase 9): el micrositio no redacta avisos propios,
 * enlaza los que el municipio ya publica por proceso para la Contraloría
 * Interna Municipal, vía el módulo de Enlaces de la Fase 6.
 */
class AvisoPrivacidadTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_responde_y_lista_los_avisos_publicados(): void
    {
        $categoria = CategoriaEnlace::factory()->create(['clave' => 'avisos-privacidad']);

        Enlace::factory()->create([
            'categoria_enlace_id' => $categoria->id,
            'nombre' => 'Sistema de Control Interno',
            'activo' => true,
        ]);

        Enlace::factory()->create([
            'categoria_enlace_id' => $categoria->id,
            'nombre' => 'Aviso retirado',
            'activo' => false,
        ]);

        $this->get(route('aviso-privacidad'))
            ->assertOk()
            ->assertSee('Sistema de Control Interno')
            ->assertDontSee('Aviso retirado');
    }

    public function test_no_falla_si_todavia_no_existe_la_categoria_de_avisos(): void
    {
        $this->get(route('aviso-privacidad'))->assertOk();
    }

    public function test_el_footer_publico_enlaza_la_pagina(): void
    {
        $this->get(route('inicio'))
            ->assertOk()
            ->assertSee(route('aviso-privacidad'), false);
    }
}
