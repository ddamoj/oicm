<?php

namespace Tests\Feature;

use App\Livewire\Admin\Direcciones\FormularioDireccion;
use App\Livewire\Admin\Direcciones\ListaDirecciones;
use App\Models\Contacto;
use App\Models\Departamento;
use App\Models\Direccion;
use App\Models\Documento;
use App\Models\PaginaInstitucional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Alta, edición, baja y autorización del catálogo de Direcciones, que
 * alimenta el organigrama público de "Quiénes somos".
 */
class GestionDireccionesTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_puede_crear_una_direccion(): void
    {
        $usuario = User::factory()->administrador()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioDireccion::class)
            ->set('nombre', 'Dirección de Auditoría Externa')
            ->set('siglas', 'DAE')
            ->set('orden', '3')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('direcciones', [
            'nombre' => 'Dirección de Auditoría Externa',
            'siglas' => 'DAE',
            'clave' => 'direccion-de-auditoria-externa',
            'orden' => 3,
            'activa' => true,
        ]);
    }

    public function test_el_nombre_es_obligatorio(): void
    {
        $usuario = User::factory()->administrador()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioDireccion::class)
            ->set('nombre', '')
            ->call('guardar')
            ->assertHasErrors(['nombre' => 'required']);
    }

    public function test_la_clave_generada_no_se_repite_entre_direcciones(): void
    {
        $usuario = User::factory()->administrador()->create();
        Direccion::factory()->create(['clave' => 'direccion-de-quejas', 'nombre' => 'Dirección de Quejas']);

        Livewire::actingAs($usuario)
            ->test(FormularioDireccion::class)
            ->set('nombre', 'Dirección de Quejas')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('direcciones', ['clave' => 'direccion-de-quejas-2']);
    }

    /**
     * La `clave` es la ruta pública `/direcciones/{clave}`: renombrar la
     * Dirección no debe romper los enlaces ya difundidos.
     */
    public function test_renombrar_una_direccion_conserva_su_clave_publica(): void
    {
        $usuario = User::factory()->administrador()->create();
        $direccion = Direccion::factory()->create([
            'clave' => 'oficina-contralor',
            'nombre' => 'Oficina del Contralor',
        ]);

        Livewire::actingAs($usuario)
            ->test(FormularioDireccion::class)
            ->call('prepararEdicion', $direccion->id)
            ->set('nombre', 'Oficina de la Contraloría')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('direcciones', [
            'id' => $direccion->id,
            'nombre' => 'Oficina de la Contraloría',
            'clave' => 'oficina-contralor',
        ]);
    }

    public function test_alternar_activa_retira_la_direccion_del_organigrama_sin_eliminarla(): void
    {
        $usuario = User::factory()->administrador()->create();
        $direccion = Direccion::factory()->create(['activa' => true]);

        Livewire::actingAs($usuario)
            ->test(ListaDirecciones::class)
            ->call('alternarActiva', $direccion->id);

        $this->assertDatabaseHas('direcciones', ['id' => $direccion->id, 'activa' => false]);
        $this->assertCount(0, Direccion::query()->activas()->get());
    }

    public function test_eliminar_una_direccion_sin_contenido_tambien_da_de_baja_sus_departamentos(): void
    {
        $usuario = User::factory()->administrador()->create();
        $direccion = Direccion::factory()->create();
        $departamento = Departamento::factory()->create(['direccion_id' => $direccion->id]);

        Livewire::actingAs($usuario)
            ->test(ListaDirecciones::class)
            ->call('eliminar', $direccion->id);

        $this->assertSoftDeleted('direcciones', ['id' => $direccion->id]);
        $this->assertSoftDeleted('departamentos', ['id' => $departamento->id]);
    }

    public function test_no_permite_eliminar_una_direccion_con_documentos_asociados(): void
    {
        $usuario = User::factory()->administrador()->create();
        $direccion = Direccion::factory()->create();
        Documento::factory()->create(['direccion_id' => $direccion->id]);

        Livewire::actingAs($usuario)
            ->test(ListaDirecciones::class)
            ->call('eliminar', $direccion->id)
            ->assertDispatched('mostrar-error');

        $this->assertDatabaseHas('direcciones', ['id' => $direccion->id, 'deleted_at' => null]);
    }

    public function test_no_permite_eliminar_una_direccion_con_paginas_o_contactos_asociados(): void
    {
        $usuario = User::factory()->administrador()->create();

        $conPagina = Direccion::factory()->create();
        PaginaInstitucional::factory()->create(['direccion_id' => $conPagina->id]);

        $conContacto = Direccion::factory()->create();
        Contacto::factory()->create(['direccion_id' => $conContacto->id]);

        Livewire::actingAs($usuario)
            ->test(ListaDirecciones::class)
            ->call('eliminar', $conPagina->id)
            ->assertDispatched('mostrar-error')
            ->call('eliminar', $conContacto->id)
            ->assertDispatched('mostrar-error');

        $this->assertDatabaseHas('direcciones', ['id' => $conPagina->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('direcciones', ['id' => $conContacto->id, 'deleted_at' => null]);
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_direcciones(): void
    {
        $this->get(route('admin.direcciones'))->assertRedirect(route('login'));
    }

    /**
     * Las Direcciones son catálogo institucional: quedan fuera del alcance
     * del rol "administrador_contenido", que sí gestiona noticias o
     * documentos.
     */
    public function test_administrador_de_contenido_no_puede_gestionar_direcciones(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        $this->actingAs($usuario)->get(route('admin.direcciones'))->assertForbidden();

        Livewire::actingAs($usuario)
            ->test(ListaDirecciones::class)
            ->assertForbidden();
    }

    public function test_administrador_de_contenido_no_puede_guardar_una_direccion(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioDireccion::class)
            ->set('nombre', 'Dirección infiltrada')
            ->call('guardar')
            ->assertForbidden();

        $this->assertDatabaseMissing('direcciones', ['nombre' => 'Dirección infiltrada']);
    }
}
