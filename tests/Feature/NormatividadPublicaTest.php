<?php

namespace Tests\Feature;

use App\Livewire\Publico\ListaNormatividadPublica;
use App\Models\Normatividad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Consulta pública del marco normativo con filtro por ámbito y buscador (Fase 7).
 */
class NormatividadPublicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_solo_se_muestran_ordenamientos_vigentes(): void
    {
        Normatividad::factory()->create(['titulo' => 'Ley vigente', 'vigente' => true]);
        Normatividad::factory()->create(['titulo' => 'Ley retirada', 'vigente' => false]);

        Livewire::test(ListaNormatividadPublica::class)
            ->assertSee('Ley vigente')
            ->assertDontSee('Ley retirada');
    }

    public function test_el_filtro_por_ambito_muestra_solo_ese_ambito(): void
    {
        Normatividad::factory()->create(['titulo' => 'Ley federal', 'ambito' => 'federal', 'vigente' => true]);
        Normatividad::factory()->create(['titulo' => 'Ley municipal', 'ambito' => 'municipal', 'vigente' => true]);

        Livewire::test(ListaNormatividadPublica::class)
            ->call('filtrarAmbito', 'federal')
            ->assertSee('Ley federal')
            ->assertDontSee('Ley municipal');
    }

    public function test_el_buscador_filtra_por_titulo(): void
    {
        Normatividad::factory()->create(['titulo' => 'Ley de Responsabilidades Administrativas', 'vigente' => true]);
        Normatividad::factory()->create(['titulo' => 'Ley de Fiscalización Superior', 'vigente' => true]);

        Livewire::test(ListaNormatividadPublica::class)
            ->set('busqueda', 'Responsabilidades')
            ->assertSee('Ley de Responsabilidades Administrativas')
            ->assertDontSee('Ley de Fiscalización Superior');
    }

    public function test_muestra_la_ficha_con_fechas_de_publicacion_y_reforma(): void
    {
        Normatividad::factory()->create([
            'titulo' => 'Ley con fechas',
            'fecha_publicacion' => '2020-01-15',
            'fecha_ultima_reforma' => '2025-06-01',
            'vigente' => true,
        ]);

        Livewire::test(ListaNormatividadPublica::class)
            ->assertSee('15/01/2020')
            ->assertSee('01/06/2025');
    }
}
