<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\View\View;

/**
 * Vista de detalle público de una noticia (RF-NOT-002). Una noticia en
 * borrador, o publicada con fecha futura, responde 404 aunque se conozca
 * su slug: nunca debe ser accesible antes de tiempo.
 */
class NoticiaController extends Controller
{
    public function show(Noticia $noticia): View
    {
        if ($noticia->estatus !== 'publicada' || ! $noticia->publicado_en || $noticia->publicado_en->isFuture()) {
            abort(404);
        }

        $otras = Noticia::query()
            ->publicado()
            ->where('id', '!=', $noticia->id)
            ->limit(3)
            ->get();

        return view('publico.noticia', [
            'noticia' => $noticia,
            'otras' => $otras,
        ]);
    }
}
