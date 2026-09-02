<?php

namespace Tests\Unit;

use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_enlace_pertenece_a_una_categoria(): void
    {
        $categoria = CategoriaEnlace::factory()->create();
        $enlace = Enlace::factory()->create(['categoria_enlace_id' => $categoria->id]);

        $this->assertTrue($enlace->categoria->is($categoria));
    }

    public function test_el_scope_publicado_excluye_enlaces_inactivos(): void
    {
        Enlace::factory()->create(['activo' => false]);
        $activo = Enlace::factory()->create(['activo' => true]);

        $resultado = Enlace::publicado()->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($activo));
    }

    public function test_el_scope_por_categoria_filtra_correctamente(): void
    {
        $categoriaUno = CategoriaEnlace::factory()->create();
        $categoriaDos = CategoriaEnlace::factory()->create();
        $enlaceUno = Enlace::factory()->create(['categoria_enlace_id' => $categoriaUno->id]);
        Enlace::factory()->create(['categoria_enlace_id' => $categoriaDos->id]);

        $resultado = Enlace::porCategoria($categoriaUno->id)->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($enlaceUno));
    }
}
