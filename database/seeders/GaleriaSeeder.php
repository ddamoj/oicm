<?php

namespace Database\Seeders;

use App\Models\Galeria;
use Illuminate\Database\Seeder;

class GaleriaSeeder extends Seeder
{
    /**
     * Galerías de muestra (Fase 8), sin archivos reales: marcador visible
     * hasta que el OICM entregue material fotográfico/de video de sus
     * eventos (mismo criterio del riesgo de contenido pendiente, AM06).
     */
    public function run(): void
    {
        $galerias = [
            [
                'titulo' => 'Capacitación en materia de responsabilidades administrativas',
                'descripcion' => 'Sesión de capacitación dirigida a personal de las Direcciones sobre la Ley de Responsabilidades Administrativas.',
                'fecha_evento' => '2026-02-12',
            ],
            [
                'titulo' => 'Sesión ordinaria del COCODI',
                'descripcion' => 'Comité de Control y Desempeño Institucional: seguimiento a los acuerdos y al Programa de Trabajo de Control Interno.',
                'fecha_evento' => '2026-03-05',
            ],
            [
                'titulo' => 'Comité de Obras Públicas',
                'descripcion' => 'Participación del OICM como órgano de control en la sesión del Comité de Obras Públicas municipal.',
                'fecha_evento' => '2026-04-18',
            ],
            [
                'titulo' => 'Proceso de entrega-recepción de la administración municipal',
                'descripcion' => 'Actos de entrega-recepción de las dependencias y entidades del Municipio de Oaxaca de Juárez.',
                'fecha_evento' => '2025-10-01',
            ],
        ];

        foreach ($galerias as $galeria) {
            Galeria::query()->updateOrCreate(
                ['titulo' => $galeria['titulo']],
                [
                    'descripcion' => $galeria['descripcion'],
                    'fecha_evento' => $galeria['fecha_evento'],
                    'publicada' => true,
                ]
            );
        }
    }
}
