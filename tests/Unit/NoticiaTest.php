<?php

namespace Tests\Unit;

use App\Models\Noticia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticiaTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_scope_publicado_excluye_borradores_y_ordena_por_mas_reciente(): void
    {
        $antigua = Noticia::factory()->create(['publicado_en' => now()->subDays(5)]);
        $reciente = Noticia::factory()->create(['publicado_en' => now()]);
        Noticia::factory()->borrador()->create();

        $resultado = Noticia::publicado()->get();

        $this->assertCount(2, $resultado);
        $this->assertTrue($resultado->first()->is($reciente));
        $this->assertTrue($resultado->last()->is($antigua));
    }

    public function test_el_scope_entre_fechas_filtra_por_rango_de_publicacion(): void
    {
        $dentro = Noticia::factory()->create(['publicado_en' => '2026-05-15']);
        Noticia::factory()->create(['publicado_en' => '2026-01-01']);

        $resultado = Noticia::query()->entreFechas('2026-05-01', '2026-05-31')->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($dentro));
    }
}
