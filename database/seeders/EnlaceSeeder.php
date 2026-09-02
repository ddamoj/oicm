<?php

namespace Database\Seeders;

use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use Illuminate\Database\Seeder;

class EnlaceSeeder extends Seeder
{
    /**
     * Enlaces institucionales tomados de "INFORMACIÓN PARA LA ATD PARA SU CARGA
     * EN EL PORTAL DEL OICM". Algunas URL provienen del documento como enlaces
     * cortos `share.google`; quedan marcadas para verificación y sustitución por
     * su URL canónica antes de publicar (riesgo documentado en el plan de trabajo, §4).
     */
    public function run(): void
    {
        $porClave = fn (string $clave) => CategoriaEnlace::query()->where('clave', $clave)->firstOrFail()->id;

        $enlaces = [
            [
                'categoria' => 'declaraciones',
                'nombre' => 'Declaración patrimonial',
                'descripcion' => 'Sistema de declaración patrimonial del Municipio de Oaxaca de Juárez.',
                'url' => 'https://declaraciones.municipiodeoaxaca.gob.mx/public/',
                'orden' => 1,
            ],
            [
                'categoria' => 'control-interno',
                'nombre' => 'Evaluación del control interno',
                'descripcion' => 'Sistema de evaluación del control interno de la administración pública municipal.',
                'url' => 'https://inspectores.municipiodeoaxaca.gob.mx/EvaluacionSist/Login.php',
                'orden' => 1,
            ],
            [
                'categoria' => 'entrega-recepcion',
                'nombre' => 'Entrega-recepción de la administración municipal',
                'descripcion' => 'Portal de entrega-recepción de la administración pública municipal y de puestos.',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/entrega-recepcion',
                'orden' => 1,
            ],
            [
                'categoria' => 'organismos-fiscalizadores',
                'nombre' => 'Auditoría Superior de la Federación (ASF)',
                'descripcion' => 'Órgano técnico de la Cámara de Diputados que fiscaliza el uso de los recursos públicos federales.',
                'url' => 'https://share.google/uRCL5e7IDb02i5pFC',
                'orden' => 1,
            ],
            [
                'categoria' => 'organismos-fiscalizadores',
                'nombre' => 'Secretaría Anticorrupción y Buen Gobierno',
                'descripcion' => 'Dependencia federal que vigila la gestión pública con eficacia, probidad y transparencia.',
                'url' => 'https://www.gob.mx/buengobierno',
                'orden' => 2,
            ],
            [
                'categoria' => 'organismos-fiscalizadores',
                'nombre' => 'Sistema Nacional Anticorrupción (SNA)',
                'descripcion' => 'Coordinación entre autoridades locales y federales para combatir la corrupción.',
                'url' => 'https://share.google/hFDLKYe77TvwENwg2',
                'orden' => 3,
            ],
            [
                'categoria' => 'organismos-fiscalizadores',
                'nombre' => 'Auditoría Superior de Fiscalización del Estado de Oaxaca (ASEO)',
                'descripcion' => 'Órgano técnico del Congreso del Estado que fiscaliza ingresos, egresos y deuda pública.',
                'url' => 'https://share.google/g2SJXriNvLnnQ9a52',
                'orden' => 4,
            ],
            [
                'categoria' => 'organismos-fiscalizadores',
                'nombre' => 'Secretaría de la Honestidad, Transparencia y de la Función Pública (Oaxaca)',
                'descripcion' => 'Dependencia estatal en materia de honestidad, transparencia y función pública.',
                'url' => 'https://share.google/GDouEVobXGP7GCjgc',
                'orden' => 5,
            ],
            [
                'categoria' => 'centros-analisis',
                'nombre' => 'México Evalúa',
                'descripcion' => 'Centro de pensamiento y análisis para la evaluación de la operación gubernamental.',
                'url' => 'https://share.google/oe7iJsSsEMMGP3Xoo',
                'orden' => 1,
            ],
            [
                'categoria' => 'centros-analisis',
                'nombre' => 'INEGI · Desarrollo Social',
                'descripcion' => 'Estudio de la pobreza y evaluación de la política de Desarrollo Social.',
                'url' => 'https://www.inegi.org.mx/desarrollosocial/evaluacion/',
                'orden' => 2,
            ],
            [
                'categoria' => 'centros-analisis',
                'nombre' => 'Instituto Mexicano de la Competitividad (IMCO)',
                'descripcion' => 'Centro de investigación en política pública para la competitividad.',
                'url' => 'https://share.google/2sP7bmMLMYbDqwQQJ',
                'orden' => 3,
            ],
        ];

        foreach ($enlaces as $enlace) {
            Enlace::query()->updateOrCreate(
                ['nombre' => $enlace['nombre']],
                [
                    'categoria_enlace_id' => $porClave($enlace['categoria']),
                    'descripcion' => $enlace['descripcion'],
                    'url' => $enlace['url'],
                    'orden' => $enlace['orden'],
                    'activo' => true,
                ]
            );
        }
    }
}
