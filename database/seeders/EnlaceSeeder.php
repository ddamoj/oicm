<?php

namespace Database\Seeders;

use App\Models\CategoriaEnlace;
use App\Models\Enlace;
use Illuminate\Database\Seeder;

class EnlaceSeeder extends Seeder
{
    /**
     * Enlaces institucionales tomados de "INFORMACIÓN PARA LA ATD PARA SU CARGA
     * EN EL PORTAL DEL OICM". El documento traía varios enlaces cortos
     * `share.google`, no resolubles desde el entorno de desarrollo (redirigen a
     * una página genérica de Google, no al destino real); se sustituyeron aquí
     * por la URL institucional oficial conocida (Fase 6, RF-ENL-001). Pendiente
     * de confirmación por el cliente antes de publicar — ver nota en
     * context/plandetrabajo.md, cierre de la Fase 6.
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
                'destacado_inicio' => true,
            ],
            [
                'categoria' => 'control-interno',
                'nombre' => 'Evaluación del control interno',
                'descripcion' => 'Sistema de evaluación del control interno de la administración pública municipal.',
                'url' => 'https://inspectores.municipiodeoaxaca.gob.mx/EvaluacionSist/Login.php',
                'orden' => 1,
                'destacado_inicio' => true,
            ],
            [
                'categoria' => 'entrega-recepcion',
                'nombre' => 'Entrega-recepción de la administración municipal',
                'descripcion' => 'Portal de entrega-recepción de la administración pública municipal y de puestos.',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/entrega-recepcion',
                'orden' => 1,
                'destacado_inicio' => true,
            ],
            [
                'categoria' => 'organismos-fiscalizadores',
                'nombre' => 'Auditoría Superior de la Federación (ASF)',
                'descripcion' => 'Órgano técnico de la Cámara de Diputados que fiscaliza el uso de los recursos públicos federales.',
                'url' => 'https://www.asf.gob.mx/',
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
                'url' => 'https://www.sna.org.mx/',
                'orden' => 3,
            ],
            [
                'categoria' => 'organismos-fiscalizadores',
                'nombre' => 'Auditoría Superior de Fiscalización del Estado de Oaxaca (ASEO)',
                'descripcion' => 'Órgano técnico del Congreso del Estado que fiscaliza ingresos, egresos y deuda pública.',
                'url' => 'https://www.asfeoaxaca.gob.mx/',
                'orden' => 4,
            ],
            [
                'categoria' => 'organismos-fiscalizadores',
                'nombre' => 'Secretaría de la Honestidad, Transparencia y de la Función Pública (Oaxaca)',
                'descripcion' => 'Dependencia estatal en materia de honestidad, transparencia y función pública.',
                'url' => 'https://www.oaxaca.gob.mx/honestidad/',
                'orden' => 5,
            ],
            [
                'categoria' => 'centros-analisis',
                'nombre' => 'México Evalúa',
                'descripcion' => 'Centro de pensamiento y análisis para la evaluación de la operación gubernamental.',
                'url' => 'https://www.mexicoevalua.org/',
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
                'url' => 'https://imco.org.mx/',
                'orden' => 3,
            ],
            // Avisos de privacidad oficiales (Fase 9): el municipio los publica por
            // proceso bajo "Contraloría Interna Municipal" en su portal de
            // transparencia. Transcritos de
            // https://transparencia.municipiodeoaxaca.gob.mx/aviso-de-privacidad/areas
            // el 03/09/2026 — pendiente de confirmación por el cliente antes de
            // publicar, mismo tratamiento que las URLs share.google de la Fase 6:
            // si el municipio rota el año del expediente el enlace puede caducar.
            [
                'categoria' => 'avisos-privacidad',
                'nombre' => 'Sistema de Control Interno',
                'descripcion' => 'Aviso de privacidad del proceso de Sistema de Control Interno del OICM.',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/docs/avisos/2025/2.%20OICM.AVS-Sistema%20de%20Control%20Interno.docx',
                'orden' => 1,
            ],
            [
                'categoria' => 'avisos-privacidad',
                'nombre' => 'Padrón de Contratistas',
                'descripcion' => 'Aviso de privacidad del proceso de Padrón de Contratistas del OICM.',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/docs/avisos/2026/6.OICM.AVS-Padron%20de%20Contratistas.docx',
                'orden' => 2,
            ],
            [
                'categoria' => 'avisos-privacidad',
                'nombre' => 'Actas de Entrega-Recepción',
                'descripcion' => 'Aviso de privacidad del proceso de Actas de Entrega del Órgano Interno de Control.',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/docs/avisos/2025/AVISO%20ORGANO%20INTERNO%20DE%20CONTROL%20ACTAS%20DE%20ENTEGA.docx',
                'orden' => 3,
            ],
            [
                'categoria' => 'avisos-privacidad',
                'nombre' => 'Dirección de Auditoría Interna',
                'descripcion' => 'Aviso de privacidad de la Dirección de Auditoría Interna.',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/docs/avisos/2026/AVISO%20DIR.%20AUDITORIA%20INTERNA%20(CONTRALORIA).docx',
                'orden' => 4,
            ],
            [
                'categoria' => 'avisos-privacidad',
                'nombre' => 'Dirección de Quejas, Denuncias e Investigación',
                'descripcion' => 'Aviso de privacidad de la Dirección de Quejas, Denuncias e Investigación (DQDISP).',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/docs/avisos/2026/AVISO%20DIR.%20QUEJAS.DENUNCIAS.INVESTIGACION%20(CONTRALORIA).docx',
                'orden' => 5,
            ],
            [
                'categoria' => 'avisos-privacidad',
                'nombre' => 'Dirección de Responsabilidades Administrativas',
                'descripcion' => 'Aviso de privacidad de la Dirección de Responsabilidades Administrativas (DRACS).',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/docs/avisos/2026/AVISO%20DIR.%20RESPONSABILIDADES%20ADMTVAS%20(CONTRALORIA).docx',
                'orden' => 6,
            ],
            [
                'categoria' => 'avisos-privacidad',
                'nombre' => 'Dirección de Control y Mejora de la Gestión Pública Municipal',
                'descripcion' => 'Aviso de privacidad de la Dirección de Control y Mejora de la Gestión Pública Municipal (DCyMGPM).',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/docs/avisos/2026/AVISO%20DIR%20CONTROL%20Y%20MEJORA%20(CONTRALORIA).docx',
                'orden' => 7,
            ],
            [
                'categoria' => 'avisos-privacidad',
                'nombre' => 'Quejas, Denuncias y Situación Patrimonial',
                'descripcion' => 'Aviso de privacidad del proceso de Quejas, Denuncias y Situación Patrimonial.',
                'url' => 'https://transparencia.municipiodeoaxaca.gob.mx/docs/avisos/2026/DIR.%20QUEJAS%2C%20DENUNCIAS%20Y%20SITUACI%C3%93N%20PAT%20(CONTRALORIA).docx',
                'orden' => 8,
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
                    'destacado_inicio' => $enlace['destacado_inicio'] ?? false,
                ]
            );
        }
    }
}
