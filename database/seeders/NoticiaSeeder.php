<?php

namespace Database\Seeders;

use App\Models\Noticia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NoticiaSeeder extends Seeder
{
    /**
     * Noticias de ejemplo para poder revisar el listado y la vista de
     * detalle públicos con contenido real durante el desarrollo (Fase 5).
     */
    public function run(): void
    {
        $autor = User::query()->where('email', 'contenido@oicm.oaxacadejuarez.gob.mx')->first();

        $noticias = [
            [
                'titulo' => 'El OICM presenta su informe anual de resultados',
                'resumen' => 'La titular del OICM presentó ante cabildo los resultados de auditoría, control interno y atención ciudadana del ejercicio.',
                'publicado_en' => now()->subDays(2),
            ],
            [
                'titulo' => 'Convocatoria abierta para el curso de responsabilidades administrativas',
                'resumen' => 'Dirigido a personas servidoras públicas municipales; incluye taller práctico sobre la Ley de Responsabilidades Administrativas.',
                'publicado_en' => now()->subDays(9),
            ],
            [
                'titulo' => 'Nuevo canal de quejas y denuncias ciudadanas',
                'resumen' => 'El OICM habilita un canal directo para que la ciudadanía reporte irregularidades de servidores públicos municipales.',
                'publicado_en' => now()->subDays(20),
            ],
        ];

        foreach ($noticias as $datos) {
            Noticia::query()->updateOrCreate(
                ['slug' => Str::slug($datos['titulo'])],
                [
                    'titulo' => $datos['titulo'],
                    'resumen' => $datos['resumen'],
                    'contenido' => '<p>'.$datos['resumen'].'</p><p>Consulta los detalles completos en las oficinas del Órgano Interno de Control Municipal o comunícate a través de los canales oficiales de contacto.</p>',
                    'estatus' => 'publicada',
                    'publicado_en' => $datos['publicado_en'],
                    'autor_id' => $autor?->id,
                ]
            );
        }
    }
}
