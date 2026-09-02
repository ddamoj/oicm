<?php

namespace Tests\Unit;

use App\Models\Direccion;
use App\Models\Documento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DireccionTest extends TestCase
{
    use RefreshDatabase;

    public function test_una_direccion_tiene_muchos_documentos(): void
    {
        $direccion = Direccion::factory()->create();
        $documento = Documento::factory()->create(['direccion_id' => $direccion->id]);

        $this->assertTrue($direccion->documentos->contains($documento));
    }

    public function test_el_scope_activas_excluye_direcciones_inactivas_y_ordena(): void
    {
        Direccion::factory()->create(['nombre' => 'Inactiva', 'activa' => false, 'orden' => 1]);
        $segunda = Direccion::factory()->create(['nombre' => 'Segunda', 'activa' => true, 'orden' => 2]);
        $primera = Direccion::factory()->create(['nombre' => 'Primera', 'activa' => true, 'orden' => 1]);

        $resultado = Direccion::activas()->get();

        $this->assertCount(2, $resultado);
        $this->assertTrue($resultado->first()->is($primera));
        $this->assertTrue($resultado->last()->is($segunda));
    }
}
