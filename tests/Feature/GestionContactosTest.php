<?php

namespace Tests\Feature;

use App\Livewire\Admin\Contactos\FormularioContacto;
use App\Livewire\Admin\Contactos\ListaContactos;
use App\Models\Contacto;
use App\Models\Direccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Alta, edición y baja del directorio de contacto (Fase 8).
 */
class GestionContactosTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_de_contenido_puede_crear_un_contacto(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $direccion = Direccion::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioContacto::class)
            ->set('nombreArea', 'Dirección de Auditoría Interna')
            ->set('direccionId', (string) $direccion->id)
            ->set('domicilio', 'Palacio Municipal, Oaxaca de Juárez, Oax.')
            ->set('correo', 'auditoria@municipiodeoaxaca.gob.mx')
            ->set('latitud', '17.0654100')
            ->set('longitud', '-96.7236500')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contactos', [
            'nombre_area' => 'Dirección de Auditoría Interna',
            'direccion_id' => $direccion->id,
            'correo' => 'auditoria@municipiodeoaxaca.gob.mx',
        ]);
    }

    public function test_nombre_de_area_es_obligatorio(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioContacto::class)
            ->set('nombreArea', '')
            ->call('guardar')
            ->assertHasErrors(['nombreArea' => 'required']);
    }

    public function test_rechaza_un_correo_con_formato_invalido(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioContacto::class)
            ->set('nombreArea', 'Contacto de prueba')
            ->set('correo', 'no-es-un-correo')
            ->call('guardar')
            ->assertHasErrors(['correo']);
    }

    public function test_rechaza_coordenadas_fuera_de_rango(): void
    {
        $usuario = User::factory()->administradorContenido()->create();

        Livewire::actingAs($usuario)
            ->test(FormularioContacto::class)
            ->set('nombreArea', 'Contacto de prueba')
            ->set('latitud', '999')
            ->call('guardar')
            ->assertHasErrors(['latitud']);
    }

    public function test_puede_editar_un_contacto_existente(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $contacto = Contacto::factory()->create(['nombre_area' => 'Nombre original', 'telefono' => '9511234567']);

        Livewire::actingAs($usuario)
            ->test(FormularioContacto::class)
            ->call('prepararEdicion', $contacto->id)
            ->set('nombreArea', 'Nombre actualizado')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contactos', ['id' => $contacto->id, 'nombre_area' => 'Nombre actualizado']);
    }

    public function test_la_baja_elimina_el_contacto(): void
    {
        $usuario = User::factory()->administradorContenido()->create();
        $contacto = Contacto::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ListaContactos::class)
            ->call('eliminar', $contacto->id);

        $this->assertDatabaseMissing('contactos', ['id' => $contacto->id]);
    }

    public function test_visitante_anonimo_no_accede_al_panel_de_contactos(): void
    {
        $this->get(route('admin.contactos'))->assertRedirect(route('login'));
    }

    public function test_usuario_sin_rol_de_contenido_no_puede_gestionar_contactos(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ListaContactos::class)
            ->assertForbidden();
    }
}
