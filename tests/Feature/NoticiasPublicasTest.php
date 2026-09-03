<?php

namespace Tests\Feature;

use App\Livewire\Publico\ListaNoticias;
use App\Models\Noticia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Listado y detalle públicos de noticias, sin autenticación (RF-NOT-002/003).
 */
class NoticiasPublicasTest extends TestCase
{
    use RefreshDatabase;

    public function test_una_noticia_en_borrador_no_es_visible_en_el_listado_ni_en_el_detalle(): void
    {
        $borrador = Noticia::factory()->borrador()->create(['titulo' => 'Aviso interno sin publicar']);

        Livewire::test(ListaNoticias::class)->assertDontSee('Aviso interno sin publicar');

        $this->get(route('noticias.mostrar', $borrador))->assertNotFound();
    }

    public function test_una_noticia_programada_a_futuro_no_es_visible_hasta_su_fecha(): void
    {
        $programada = Noticia::factory()->create([
            'titulo' => 'Noticia programada',
            'publicado_en' => now()->addDays(3),
        ]);

        Livewire::test(ListaNoticias::class)->assertDontSee('Noticia programada');
        $this->get(route('noticias.mostrar', $programada))->assertNotFound();

        $this->travelTo(now()->addDays(4));

        Livewire::test(ListaNoticias::class)->assertSee('Noticia programada');
        $this->get(route('noticias.mostrar', $programada))->assertOk();
    }

    public function test_el_listado_ordena_de_la_mas_reciente_a_la_mas_antigua(): void
    {
        $antigua = Noticia::factory()->create(['titulo' => 'Noticia antigua', 'publicado_en' => now()->subDays(10)]);
        $reciente = Noticia::factory()->create(['titulo' => 'Noticia reciente', 'publicado_en' => now()->subDay()]);

        $componente = Livewire::test(ListaNoticias::class);
        $titulos = $componente->viewData('noticias')->pluck('titulo')->all();

        $this->assertSame(['Noticia reciente', 'Noticia antigua'], array_values(array_filter(
            $titulos,
            fn ($titulo) => in_array($titulo, ['Noticia reciente', 'Noticia antigua'], true)
        )));
    }

    public function test_el_listado_pagina_los_resultados(): void
    {
        Noticia::factory()->count(10)->create();

        $componente = Livewire::test(ListaNoticias::class);

        $this->assertCount(9, $componente->viewData('noticias')->items());
        $this->assertSame(2, $componente->viewData('noticias')->lastPage());
    }

    public function test_busqueda_por_palabra_clave(): void
    {
        Noticia::factory()->create(['titulo' => 'Convocatoria de auditoría interna']);
        Noticia::factory()->create(['titulo' => 'Aviso de mantenimiento del portal']);

        Livewire::test(ListaNoticias::class)
            ->set('busqueda', 'auditoría')
            ->assertSee('Convocatoria de auditoría interna')
            ->assertDontSee('Aviso de mantenimiento del portal');
    }

    public function test_busqueda_por_rango_de_fechas(): void
    {
        $dentro = Noticia::factory()->create(['titulo' => 'Dentro del rango', 'publicado_en' => '2026-05-15']);
        Noticia::factory()->create(['titulo' => 'Fuera del rango', 'publicado_en' => '2026-01-01']);

        Livewire::test(ListaNoticias::class)
            ->set('desde', '2026-05-01')
            ->set('hasta', '2026-05-31')
            ->assertSee('Dentro del rango')
            ->assertDontSee('Fuera del rango');

        $this->assertTrue($dentro->publicado_en->between('2026-05-01', '2026-05-31'));
    }

    public function test_busqueda_sin_resultados_muestra_mensaje(): void
    {
        Noticia::factory()->create(['titulo' => 'Noticia existente']);

        Livewire::test(ListaNoticias::class)
            ->set('busqueda', 'palabra que no existe en ninguna noticia')
            ->assertSee('Sin noticias que coincidan');
    }
}
