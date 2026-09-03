<?php

namespace Tests\Feature;

use App\Models\Departamento;
use App\Models\Direccion;
use App\Models\PaginaInstitucional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Visibilidad pública de "Quiénes somos", el organigrama y las páginas por
 * Dirección (Fase 7, RF-INS).
 */
class InformacionInstitucionalPublicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_quienes_somos_muestra_el_contenido_publicado(): void
    {
        PaginaInstitucional::factory()->create([
            'slug' => 'quienes-somos',
            'direccion_id' => null,
            'titulo' => 'Quiénes somos',
            'contenido' => '<h2>Misión</h2><p>Texto de la misión institucional.</p>',
            'estatus' => 'publicada',
        ]);

        $this->get(route('quienes-somos'))
            ->assertOk()
            ->assertSee('Texto de la misión institucional.', false);
    }

    public function test_una_pagina_en_borrador_no_es_visible_en_quienes_somos(): void
    {
        PaginaInstitucional::factory()->create([
            'slug' => 'quienes-somos',
            'direccion_id' => null,
            'contenido' => '<p>Borrador sin publicar todavía.</p>',
            'estatus' => 'borrador',
        ]);

        $this->get(route('quienes-somos'))
            ->assertOk()
            ->assertDontSee('Borrador sin publicar todavía.');
    }

    public function test_el_organigrama_muestra_las_direcciones_activas_y_sus_departamentos(): void
    {
        $direccion = Direccion::factory()->create(['nombre' => 'Dirección de Prueba', 'activa' => true]);
        Departamento::factory()->create(['direccion_id' => $direccion->id, 'nombre' => 'Departamento de Prueba', 'activo' => true]);

        $this->get(route('quienes-somos'))
            ->assertOk()
            ->assertSee('Dirección de Prueba')
            ->assertSee('Departamento de Prueba');
    }

    public function test_la_ficha_de_una_direccion_muestra_su_pagina_y_departamentos(): void
    {
        $direccion = Direccion::factory()->create(['clave' => 'direccion-prueba', 'nombre' => 'Dirección de Prueba', 'activa' => true]);
        Departamento::factory()->create(['direccion_id' => $direccion->id, 'nombre' => 'Departamento X', 'activo' => true]);
        PaginaInstitucional::factory()->create([
            'direccion_id' => $direccion->id,
            'slug' => 'direccion-direccion-prueba',
            'contenido' => '<p>Procesos y oficios de la dirección de prueba.</p>',
            'estatus' => 'publicada',
        ]);

        $this->get(route('direcciones.mostrar', $direccion))
            ->assertOk()
            ->assertSee('Procesos y oficios de la dirección de prueba.', false)
            ->assertSee('Departamento X');
    }

    public function test_una_direccion_inactiva_responde_404(): void
    {
        $direccion = Direccion::factory()->create(['clave' => 'inactiva', 'activa' => false]);

        $this->get(route('direcciones.mostrar', $direccion))->assertNotFound();
    }

    public function test_el_listado_de_direcciones_solo_muestra_direcciones_activas(): void
    {
        Direccion::factory()->create(['nombre' => 'Dirección visible', 'activa' => true]);
        Direccion::factory()->create(['nombre' => 'Dirección oculta', 'activa' => false]);

        $this->get(route('direcciones'))
            ->assertOk()
            ->assertSee('Dirección visible')
            ->assertDontSee('Dirección oculta');
    }
}
