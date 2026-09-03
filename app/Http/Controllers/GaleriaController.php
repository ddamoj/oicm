<?php

namespace App\Http\Controllers;

use App\Models\Galeria;
use Illuminate\View\View;

/**
 * Galería pública de fotos y videos por evento (Fase 8): grid de galerías
 * publicadas y detalle con lightbox. Una galería despublicada o eliminada
 * responde 404 aunque se conozca su identificador.
 */
class GaleriaController extends Controller
{
    public function index(): View
    {
        $galerias = Galeria::query()
            ->publicado()
            ->with(['medios' => fn ($consulta) => $consulta->orderBy('orden')->limit(1)])
            ->withCount('medios')
            ->paginate(12);

        return view('publico.galeria', ['galerias' => $galerias]);
    }

    public function show(Galeria $galeria): View
    {
        if (! $galeria->publicada) {
            abort(404);
        }

        $galeria->load(['medios' => fn ($consulta) => $consulta->orderBy('orden')]);

        return view('publico.galeria-detalle', ['galeria' => $galeria]);
    }
}
