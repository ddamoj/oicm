<?php

namespace Tests\Unit;

use App\Models\Estrado;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstradoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_scope_publicado_excluye_estrados_inactivos(): void
    {
        Estrado::factory()->create(['activo' => false]);
        $activo = Estrado::factory()->create(['activo' => true]);

        $resultado = Estrado::publicado()->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($activo));
    }

    public function test_el_scope_buscar_filtra_por_numero_expediente_o_asunto(): void
    {
        $coincide = Estrado::factory()->create(['asunto' => 'Notificación de resolución', 'expediente' => 'OICM/DRACS/001/2026']);
        Estrado::factory()->create(['asunto' => 'Otro asunto distinto', 'expediente' => 'OICM/DRACS/002/2026']);

        $resultado = Estrado::buscar('resolución')->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($coincide));
    }

    public function test_el_numero_de_folio_es_unico(): void
    {
        Estrado::factory()->create(['numero' => 10]);

        $this->expectException(QueryException::class);

        Estrado::factory()->create(['numero' => 10]);
    }
}
