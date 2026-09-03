<?php

namespace Tests\Feature;

use App\Livewire\Perfil\CambiarContrasena;
use App\Livewire\Perfil\DatosPersonales;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PerfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_actualizar_sus_datos_personales(): void
    {
        $usuario = User::factory()->administrador()->create();

        Livewire::actingAs($usuario)
            ->test(DatosPersonales::class)
            ->set('name', 'Nombre Actualizado')
            ->set('email', 'actualizado@oicm.oaxacadejuarez.gob.mx')
            ->call('guardar')
            ->assertHasNoErrors();

        $usuario->refresh();
        $this->assertSame('Nombre Actualizado', $usuario->name);
        $this->assertSame('actualizado@oicm.oaxacadejuarez.gob.mx', $usuario->email);
    }

    public function test_puede_cambiar_su_contrasena_con_la_contrasena_actual_correcta(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Actual2026')]);

        Livewire::actingAs($usuario)
            ->test(CambiarContrasena::class)
            ->set('current_password', 'Oicm#Actual2026')
            ->set('password', 'Oicm#Nueva2027')
            ->set('password_confirmation', 'Oicm#Nueva2027')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('Oicm#Nueva2027', $usuario->fresh()->password));
    }

    public function test_rechaza_el_cambio_si_la_contrasena_actual_es_incorrecta(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Actual2026')]);

        Livewire::actingAs($usuario)
            ->test(CambiarContrasena::class)
            ->set('current_password', 'contrasena-equivocada')
            ->set('password', 'Oicm#Nueva2027')
            ->set('password_confirmation', 'Oicm#Nueva2027')
            ->call('guardar')
            ->assertHasErrors('current_password');

        $this->assertTrue(Hash::check('Oicm#Actual2026', $usuario->fresh()->password));
    }

    public function test_aplica_la_politica_de_contrasena_al_cambiarla(): void
    {
        $usuario = User::factory()->administrador()->create(['password' => bcrypt('Oicm#Actual2026')]);

        Livewire::actingAs($usuario)
            ->test(CambiarContrasena::class)
            ->set('current_password', 'Oicm#Actual2026')
            ->set('password', 'debil')
            ->set('password_confirmation', 'debil')
            ->call('guardar')
            ->assertHasErrors('password');
    }
}
