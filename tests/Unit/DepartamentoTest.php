<?php

namespace Tests\Unit;

use App\Models\Departamento;
use App\Models\Direccion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartamentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_departamento_pertenece_a_una_direccion(): void
    {
        $direccion = Direccion::factory()->create();
        $departamento = Departamento::factory()->create(['direccion_id' => $direccion->id]);

        $this->assertTrue($departamento->direccion->is($direccion));
    }

    public function test_el_scope_activos_excluye_departamentos_inactivos(): void
    {
        $direccion = Direccion::factory()->create();
        Departamento::factory()->create(['direccion_id' => $direccion->id, 'activo' => false]);
        $activo = Departamento::factory()->create(['direccion_id' => $direccion->id, 'activo' => true]);

        $resultado = Departamento::activos()->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($activo));
    }
}
