<?php

namespace Tests\Unit;

use App\Models\Normatividad;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NormatividadTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_scope_por_ambito_filtra_correctamente(): void
    {
        $federal = Normatividad::factory()->create(['ambito' => 'federal']);
        Normatividad::factory()->create(['ambito' => 'estatal']);
        Normatividad::factory()->create(['ambito' => 'municipal']);

        $resultado = Normatividad::porAmbito('federal')->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($federal));
    }

    public function test_el_scope_publicado_excluye_normatividad_no_vigente(): void
    {
        Normatividad::factory()->create(['vigente' => false]);
        $vigente = Normatividad::factory()->create(['vigente' => true]);

        $resultado = Normatividad::publicado()->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($vigente));
    }

    public function test_las_fechas_se_convierten_a_instancias_carbon(): void
    {
        $normatividad = Normatividad::factory()->create([
            'fecha_publicacion' => '2016-07-18',
            'fecha_ultima_reforma' => '2025-12-15',
        ]);

        $this->assertInstanceOf(Carbon::class, $normatividad->fecha_publicacion);
        $this->assertInstanceOf(Carbon::class, $normatividad->fecha_ultima_reforma);
    }
}
