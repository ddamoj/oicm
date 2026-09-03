<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\View\View;

/**
 * Página pública de Contacto (Fase 8): directorio agrupado por Dirección,
 * mapa embebido de las coordenadas capturadas y canal de quejas y denuncias.
 */
class ContactoController extends Controller
{
    public function __invoke(): View
    {
        $contactos = Contacto::query()->with('direccion')->ordenado()->get();

        return view('publico.contacto', [
            'contactos' => $contactos,
            'canalQuejas' => $contactos->firstWhere('canal_quejas_denuncias', '!=', null),
        ]);
    }
}
