<?php

namespace App\Http\Controllers;

use App\Models\CategoriaEnlace;
use App\Models\Departamento;
use App\Models\Direccion;
use App\Models\PaginaInstitucional;
use Illuminate\View\View;

/**
 * Información institucional pública (Fase 7, RF-INS): "Quiénes somos" con su
 * organigrama interactivo, y la ficha de cada Dirección con sus procesos,
 * oficios, formatos y departamentos.
 */
class InstitucionalController extends Controller
{
    public function quienesSomos(): View
    {
        $pagina = PaginaInstitucional::query()
            ->publicado()
            ->whereNull('direccion_id')
            ->where('slug', 'quienes-somos')
            ->first();

        return view('publico.quienes-somos', [
            'pagina' => $pagina,
            'direcciones' => Direccion::activas()->with(['departamentos' => fn ($consulta) => $consulta->activos()])->get(),
        ]);
    }

    public function listaDirecciones(): View
    {
        return view('publico.direcciones', [
            'direcciones' => Direccion::activas()->get(),
        ]);
    }

    public function direccion(Direccion $direccion): View
    {
        if (! $direccion->activa) {
            abort(404);
        }

        $pagina = PaginaInstitucional::query()
            ->publicado()
            ->where('direccion_id', $direccion->id)
            ->first();

        return view('publico.direccion', [
            'direccion' => $direccion,
            'pagina' => $pagina,
            'departamentos' => Departamento::query()->where('direccion_id', $direccion->id)->activos()->get(),
        ]);
    }

    /**
     * Aviso de privacidad (Fase 9): el micrositio no redacta un aviso propio,
     * enlaza los que el municipio ya publica por proceso para la Contraloría
     * Interna Municipal (nombre con el que aparece el OICM en ese portal), vía
     * el mismo módulo de Enlaces de la Fase 6.
     */
    public function avisoPrivacidad(): View
    {
        $categoria = CategoriaEnlace::query()
            ->where('clave', 'avisos-privacidad')
            ->with(['enlaces' => fn ($consulta) => $consulta->publicado()])
            ->first();

        return view('publico.aviso-privacidad', [
            'avisos' => $categoria?->enlaces ?? collect(),
        ]);
    }
}
