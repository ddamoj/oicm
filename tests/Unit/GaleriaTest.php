<?php

namespace Tests\Unit;

use App\Models\Galeria;
use App\Models\GaleriaMedio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GaleriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_una_galeria_tiene_muchos_medios(): void
    {
        $galeria = Galeria::factory()->create();
        $medio = GaleriaMedio::factory()->create(['galeria_id' => $galeria->id]);

        $this->assertTrue($galeria->medios->contains($medio));
    }

    public function test_el_scope_publicado_excluye_galerias_no_publicadas(): void
    {
        Galeria::factory()->create(['publicada' => false]);
        $publicada = Galeria::factory()->create(['publicada' => true]);

        $resultado = Galeria::publicado()->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($publicada));
    }
}
