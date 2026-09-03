<?php

namespace Database\Seeders;

use App\Models\Direccion;
use App\Models\PaginaInstitucional;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaginaInstitucionalSeeder extends Seeder
{
    /**
     * Contenido institucional inicial (Fase 7), tomado de forma literal del
     * documento "INFORMACIÓN PARA LA ATD PARA SU CARGA EN EL PORTAL DEL
     * OICM.pdf": historia (origen 2002 → administración 2025-2027), misión,
     * visión y los 8 valores institucionales, más una página por Dirección.
     *
     * PENDIENTE DE CONFIRMACIÓN: el apartado "OBJETIVOS" del documento de
     * carga llega vacío (solo dice "Desarrollar"); se deja un marcador visible
     * hasta que el cliente entregue el texto — mismo tratamiento que las URL
     * pendientes de la Fase 6 (ver `context/plandetrabajo.md`).
     */
    public function run(): void
    {
        $autor = User::query()->where('email', 'contenido@oicm.oaxacadejuarez.gob.mx')->first();

        PaginaInstitucional::query()->updateOrCreate(
            ['slug' => 'quienes-somos'],
            [
                'direccion_id' => null,
                'titulo' => 'Quiénes somos',
                'estatus' => 'publicada',
                'actualizado_por' => $autor?->id,
                'contenido' => $this->contenidoQuienesSomos(),
            ]
        );

        foreach ($this->contenidoPorDireccion() as $claveDireccion => $contenido) {
            $direccion = Direccion::query()->where('clave', $claveDireccion)->first();

            if (! $direccion) {
                continue;
            }

            PaginaInstitucional::query()->updateOrCreate(
                ['slug' => 'direccion-'.$claveDireccion],
                [
                    'direccion_id' => $direccion->id,
                    'titulo' => $direccion->nombre,
                    'estatus' => 'publicada',
                    'actualizado_por' => $autor?->id,
                    'contenido' => $contenido,
                ]
            );
        }
    }

    private function contenidoQuienesSomos(): string
    {
        return <<<'HTML'
            <h2>Historia</h2>
            <p>El Órgano Interno de Control en el Municipio de Oaxaca de Juárez tiene su origen en el año 2002, cuando fue aprobado bajo la denominación de Contraloría Municipal, con funciones orientadas principalmente a la realización de auditorías y a la supervisión del avance físico de los programas de inversión pública municipal.</p>
            <p>A partir de 2008 amplió sus atribuciones al agregarse la supervisión del avance físico y financiero de los programas de inversión pública, así como el control y evaluación de la administración pública municipal. Para 2013 consolidó sus atribuciones en materia de auditoría, fiscalización, control y evaluación de la gestión pública municipal.</p>
            <p>En 2019 cambió de denominación a Dirección de Contraloría Municipal. Durante la administración 2022–2024, en cumplimiento de la Ley Orgánica Municipal del Estado de Oaxaca, se llevó a cabo una reorganización institucional que cambió su nomenclatura a Órgano Interno de Control Municipal, separando las funciones de investigación y substanciación de responsabilidades en dos direcciones: Quejas, Denuncias, Investigación y Situación Patrimonial; y Responsabilidades Administrativas, Controversias y Sanciones. Se creó además la Dirección de Control y Mejora de la Gestión Pública Municipal.</p>
            <p>En la administración municipal 2025-2027, el Órgano Interno de Control Municipal transita hacia un enfoque más preventivo y de fortalecimiento del control interno, en alineación con las reformas a la Ley Orgánica Municipal del Estado de Oaxaca publicadas en 2025 y 2026.</p>

            <h2>Misión</h2>
            <p>Contribuir al fortalecimiento de la gestión pública municipal mediante la vigilancia, fiscalización y evaluación del uso de los recursos públicos, garantizando la atención oportuna de quejas, denuncias e inconformidades, promoviendo el control interno, la transparencia, la rendición de cuentas y el cumplimiento de la legalidad en la administración pública municipal.</p>

            <h2>Visión</h2>
            <p>Consolidarse como un Órgano Interno de Control Municipal confiable, independiente y eficiente, reconocido por su capacidad técnica y ética en la prevención, detección y sanción de actos de corrupción. Ser referente en el fortalecimiento del control interno, la mejora continua de la gestión pública y la generación de confianza ciudadana en la Administración Pública Municipal.</p>

            <h2>Valores</h2>
            <ul>
                <li><strong>Interés público:</strong> actuar buscando en todo momento la máxima atención de las necesidades y demandas de la sociedad por encima de intereses particulares.</li>
                <li><strong>Respeto:</strong> conducirse con austeridad y sin ostentación, con trato digno y cordial hacia todas las personas.</li>
                <li><strong>Respeto a los derechos humanos:</strong> respetar, garantizar, promover y proteger los derechos humanos conforme a los principios de universalidad, interdependencia, indivisibilidad y progresividad.</li>
                <li><strong>Igualdad y no discriminación:</strong> prestar los servicios a todas las personas sin distinción, exclusión, restricción o preferencia.</li>
                <li><strong>Equidad de género:</strong> garantizar que mujeres y hombres accedan en las mismas condiciones a los bienes, programas y beneficios institucionales.</li>
                <li><strong>Entorno cultural y ecológico:</strong> evitar la afectación del patrimonio cultural y de los ecosistemas, promoviendo su protección y conservación.</li>
                <li><strong>Cooperación:</strong> colaborar y propiciar el trabajo en equipo para alcanzar los objetivos comunes previstos en los planes y programas gubernamentales.</li>
                <li><strong>Liderazgo:</strong> ser guía, ejemplo y promotoras del Código de Ética y las Reglas de Integridad.</li>
            </ul>

            <h2>Objetivos</h2>
            <p><em>Contenido institucional pendiente de confirmación por el OICM: el documento de carga entregado por el cliente no incluye el texto de este apartado.</em></p>
            HTML;
    }

    /**
     * @return array<string, string>
     */
    private function contenidoPorDireccion(): array
    {
        return [
            'oficina-contralor' => '<p>Titularidad del Órgano Interno de Control Municipal. Coordina a las cuatro direcciones de área y representa al órgano ante las demás dependencias municipales y organismos externos de fiscalización.</p>',
            'auditoria-interna' => '<p>La Dirección de Auditoría Interna (DAI) realiza auditorías, revisiones y acciones de control sobre la gestión pública municipal, así como la supervisión de los avances físicos y financieros de los programas de inversión y obra pública.</p>',
            'control-mejora-gestion' => '<p>La Dirección de Control y Mejora de la Gestión Pública Municipal (DCyMGPM) está orientada a la prevención de riesgos, la implementación del control interno y la mejora de procesos de la administración pública municipal.</p>',
            'quejas-denuncias-patrimonial' => '<p>La Dirección de Quejas, Denuncias, Investigación y Situación Patrimonial (DQDISP) atiende quejas, denuncias e investigaciones, y da seguimiento a la situación patrimonial de las personas servidoras públicas municipales.</p>',
            'responsabilidades-controversias-sanciones' => '<p>La Dirección de Responsabilidades Administrativas, Controversias y Sanciones (DRACS) substancia los procedimientos de responsabilidad administrativa y opera los estrados digitales para las notificaciones correspondientes.</p>',
        ];
    }
}
