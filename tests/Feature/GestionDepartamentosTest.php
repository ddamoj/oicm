<?php

namespace Tests\Feature;

use App\Livewire\Admin\Direcciones\FormularioDepartamento;
use App\Livewire\Admin\Direcciones\ListaDepartamentos;
use App\Models\Departamento;
use App\Models\Direccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Departamentos de cada Dirección: el segundo nivel del organigrama público.
 * Cubre además el motivo por el que se construyó la pantalla — corregir los
 * nombres provisionales sembrados en la Fase 7 sin tocar código.
 */
class GestionDepartamentosTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_puede_crear_un_departamento_en_una_direccion(): void
    {
        $usuario = User::factory()->administrador()->create();
        $direccion = Direccion::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioDepartamento::class)
            ->call('prepararAlta', $direccion->id)
            ->set('nombre', 'Departamento de Auditoría Financiera')
            ->set('orden', '2')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('departamentos', [
            'direccion_id' => $direccion->id,
            'nombre' => 'Departamento de Auditoría Financiera',
            'orden' => 2,
            'activo' => true,
        ]);
    }

    public function test_el_nombre_del_departamento_es_obligatorio(): void
    {
        $usuario = User::factory()->administrador()->create();
        $direccion = Direccion::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioDepartamento::class)
            ->call('prepararAlta', $direccion->id)
            ->set('nombre', '')
            ->call('guardar')
            ->assertHasErrors(['nombre' => 'required']);
    }

    /**
     * El caso de uso que motivó el módulo: los 11 nombres sembrados en la
     * Fase 7 son provisionales y el OICM debe poder corregirlos.
     */
    public function test_puede_corregir_el_nombre_provisional_de_un_departamento(): void
    {
        $usuario = User::factory()->administrador()->create();
        $departamento = Departamento::factory()->create(['nombre' => 'Departamento provisional']);

        Livewire::actingAs($usuario)
            ->test(FormularioDepartamento::class)
            ->call('prepararEdicion', $departamento->id)
            ->set('nombre', 'Departamento de Situación Patrimonial')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('departamentos', [
            'id' => $departamento->id,
            'nombre' => 'Departamento de Situación Patrimonial',
        ]);
    }

    public function test_alternar_activo_retira_el_departamento_del_organigrama_sin_eliminarlo(): void
    {
        $usuario = User::factory()->administrador()->create();
        $direccion = Direccion::factory()->create();
        $departamento = Departamento::factory()->create(['direccion_id' => $direccion->id, 'activo' => true]);

        Livewire::actingAs($usuario)
            ->test(ListaDepartamentos::class, ['direccion' => $direccion])
            ->call('alternarActivo', $departamento->id);

        $this->assertDatabaseHas('departamentos', ['id' => $departamento->id, 'activo' => false]);
        $this->assertCount(0, $direccion->departamentos()->activos()->get());
    }

    public function test_eliminar_da_de_baja_logica_el_departamento(): void
    {
        $usuario = User::factory()->administrador()->create();
        $direccion = Direccion::factory()->create();
        $departamento = Departamento::factory()->create(['direccion_id' => $direccion->id]);

        Livewire::actingAs($usuario)
            ->test(ListaDepartamentos::class, ['direccion' => $direccion])
            ->call('eliminar', $departamento->id);

        $this->assertSoftDeleted('departamentos', ['id' => $departamento->id]);
    }

    /**
     * Un id manipulado desde el cliente no debe alcanzar departamentos de
     * otra Dirección.
     */
    public function test_no_puede_operar_sobre_un_departamento_de_otra_direccion(): void
    {
        $usuario = User::factory()->administrador()->create();
        $propia = Direccion::factory()->create();
        $ajena = Direccion::factory()->create();
        $departamentoAjeno = Departamento::factory()->create(['direccion_id' => $ajena->id, 'activo' => true]);

        Livewire::actingAs($usuario)
            ->test(ListaDepartamentos::class, ['direccion' => $propia])
            ->call('eliminar', $departamentoAjeno->id)
            ->assertDispatched('mostrar-error');

        $this->assertDatabaseHas('departamentos', ['id' => $departamentoAjeno->id, 'deleted_at' => null]);
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_departamentos(): void
    {
        $direccion = Direccion::factory()->create();

        $this->get(route('admin.direcciones.departamentos', $direccion))->assertRedirect(route('login'));
    }

    public function test_administrador_de_contenido_no_puede_gestionar_departamentos(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $direccion = Direccion::factory()->create();

        $this->actingAs($usuario)
            ->get(route('admin.direcciones.departamentos', $direccion))
            ->assertForbidden();

        Livewire::actingAs($usuario)
            ->test(ListaDepartamentos::class, ['direccion' => $direccion])
            ->assertForbidden();
    }
}
