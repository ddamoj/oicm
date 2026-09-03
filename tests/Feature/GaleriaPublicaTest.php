<?php

namespace Tests\Feature;

use App\Models\Galeria;
use App\Models\GaleriaMedio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Grid público de galerías y detalle con lightbox (Fase 8).
 */
class GaleriaPublicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_grid_publico_solo_muestra_galerias_publicadas(): void
    {
        Galeria::factory()->create(['titulo' => 'Galería visible', 'publicada' => true]);
        Galeria::factory()->create(['titulo' => 'Galería oculta', 'publicada' => false]);

        $this->get(route('galeria'))
            ->assertOk()
            ->assertSee('Galería visible')
            ->assertDontSee('Galería oculta');
    }

    public function test_una_galeria_despublicada_responde_404(): void
    {
        $galeria = Galeria::factory()->create(['publicada' => false]);

        $this->get(route('galeria.mostrar', $galeria))->assertNotFound();
    }

    public function test_el_detalle_muestra_los_medios_de_la_galeria(): void
    {
        $galeria = Galeria::factory()->create(['publicada' => true]);
        GaleriaMedio::factory()->create([
            'galeria_id' => $galeria->id,
            'tipo' => 'foto',
            'descripcion_alt' => 'Foto de la capacitación',
        ]);

        $this->get(route('galeria.mostrar', $galeria))
            ->assertOk()
            ->assertSee('Foto de la capacitación');
    }

    public function test_una_galeria_eliminada_responde_404(): void
    {
        $galeria = Galeria::factory()->create(['publicada' => true]);
        $galeria->delete();

        $this->get(route('galeria.mostrar', $galeria))->assertNotFound();
    }
}
