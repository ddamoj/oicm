<?php

namespace Tests\Unit;

use App\Models\PaginaInstitucional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginaInstitucionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_scope_publicado_excluye_borradores(): void
    {
        PaginaInstitucional::factory()->borrador()->create();
        $publicada = PaginaInstitucional::factory()->create(['estatus' => 'publicada']);

        $resultado = PaginaInstitucional::publicado()->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($publicada));
    }

    public function test_una_pagina_pertenece_a_una_direccion(): void
    {
        $pagina = PaginaInstitucional::factory()->create();

        $this->assertNotNull($pagina->direccion);
    }
}
