<?php

namespace Database\Seeders;

use App\Models\Normatividad;
use Illuminate\Database\Seeder;

class NormatividadSeeder extends Seeder
{
    /**
     * Marco normativo federal, estatal y municipal del OICM, tomado literalmente
     * del documento "INFORMACIÓN PARA LA ATD PARA SU CARGA EN EL PORTAL DEL OICM".
     */
    public function run(): void
    {
        $orden = 0;

        foreach ($this->federal() as $item) {
            $this->guardar('federal', ++$orden, $item);
        }

        $orden = 0;
        foreach ($this->estatal() as $item) {
            $this->guardar('estatal', ++$orden, $item);
        }

        $orden = 0;
        foreach ($this->municipal() as $item) {
            $this->guardar('municipal', ++$orden, $item);
        }
    }

    /**
     * @param  array{titulo: string, medio: string, publicacion: ?string, reforma: ?string}  $item
     */
    private function guardar(string $ambito, int $orden, array $item): void
    {
        Normatividad::query()->updateOrCreate(
            ['ambito' => $ambito, 'titulo' => $item['titulo']],
            [
                'medio_publicacion' => $item['medio'],
                'fecha_publicacion' => $item['publicacion'],
                'fecha_ultima_reforma' => $item['reforma'],
                'orden' => $orden,
                'vigente' => true,
            ]
        );
    }

    /**
     * @return array<int, array{titulo: string, medio: string, publicacion: ?string, reforma: ?string}>
     */
    private function federal(): array
    {
        $dof = 'Diario Oficial de la Federación';

        return [
            ['titulo' => 'Constitución Política de los Estados Unidos Mexicanos', 'medio' => $dof, 'publicacion' => '1917-02-05', 'reforma' => '2026-06-02'],
            ['titulo' => 'Ley General de Responsabilidades Administrativas', 'medio' => $dof, 'publicacion' => '2016-07-18', 'reforma' => '2025-12-15'],
            ['titulo' => 'Ley General del Sistema Nacional Anticorrupción', 'medio' => $dof, 'publicacion' => '2016-07-18', 'reforma' => '2021-05-20'],
            ['titulo' => 'Ley de Adquisiciones, Arrendamientos y Servicios del Sector Público', 'medio' => $dof, 'publicacion' => '2025-04-16', 'reforma' => null],
            ['titulo' => 'Ley de Coordinación Fiscal', 'medio' => $dof, 'publicacion' => '1978-12-27', 'reforma' => '2024-01-03'],
            ['titulo' => 'Ley General de Desarrollo Social', 'medio' => $dof, 'publicacion' => '2004-01-20', 'reforma' => '2026-01-15'],
            ['titulo' => 'Ley de Amparo', 'medio' => $dof, 'publicacion' => '2013-04-02', 'reforma' => '2025-10-16'],
            ['titulo' => 'Ley de Disciplina Financiera de las Entidades Federativas y los Municipios', 'medio' => $dof, 'publicacion' => '2016-04-27', 'reforma' => '2022-05-10'],
            ['titulo' => 'Ley de Fiscalización y Rendición de Cuentas de la Federación', 'medio' => $dof, 'publicacion' => '2016-07-18', 'reforma' => '2026-05-14'],
            ['titulo' => 'Ley de Obras Públicas y Servicios Relacionados con las Mismas', 'medio' => $dof, 'publicacion' => '2000-01-04', 'reforma' => '2025-11-14'],
            ['titulo' => 'Ley Federal de Presupuesto y Responsabilidad Hacendaria', 'medio' => $dof, 'publicacion' => '2006-03-30', 'reforma' => '2026-04-09'],
            ['titulo' => 'Ley General de Contabilidad Gubernamental', 'medio' => $dof, 'publicacion' => '2008-12-31', 'reforma' => '2026-05-14'],
            ['titulo' => 'Ley General de Transparencia y Acceso a la Información Pública', 'medio' => $dof, 'publicacion' => '2025-03-20', 'reforma' => null],
            ['titulo' => 'Reglamento del Código Fiscal de la Federación', 'medio' => $dof, 'publicacion' => '2014-04-02', 'reforma' => null],
            ['titulo' => 'Reglamento de la Ley de Adquisiciones, Arrendamientos y Servicios del Sector Público', 'medio' => $dof, 'publicacion' => '2025-12-18', 'reforma' => '2026-03-27'],
            ['titulo' => 'Reglamento de la Ley de Obras Públicas y Servicios Relacionados con las Mismas', 'medio' => $dof, 'publicacion' => '2010-07-28', 'reforma' => '2023-02-24'],
            ['titulo' => 'Acuerdo por el que se emiten las Disposiciones y el Manual Administrativo de Aplicación General en Materia de Control Interno', 'medio' => $dof, 'publicacion' => '2016-11-03', 'reforma' => '2018-09-05'],
            ['titulo' => 'Acuerdo por el que se emiten los Lineamientos del Fondo de Aportaciones para la Infraestructura Social', 'medio' => $dof, 'publicacion' => '2026-02-24', 'reforma' => null],
            ['titulo' => 'Decreto por el que se formula la declaratoria de las zonas de atención prioritaria para el año 2026', 'medio' => $dof, 'publicacion' => '2025-11-21', 'reforma' => null],
            ['titulo' => 'Marco Integrado de Control Interno', 'medio' => 'Auditoría Superior de la Federación (ASF) y Secretaría de la Función Pública', 'publicacion' => '2014-01-01', 'reforma' => null],
        ];
    }

    /**
     * @return array<int, array{titulo: string, medio: string, publicacion: ?string, reforma: ?string}>
     */
    private function estatal(): array
    {
        $pogeo = 'Periódico Oficial del Gobierno del Estado de Oaxaca';

        return [
            ['titulo' => 'Constitución Política del Estado Libre y Soberano de Oaxaca', 'medio' => $pogeo, 'publicacion' => '1922-04-04', 'reforma' => '2026-01-10'],
            ['titulo' => 'Ley Orgánica Municipal del Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2010-11-30', 'reforma' => '2026-01-10'],
            ['titulo' => 'Ley de Adquisiciones, Enajenaciones, Arrendamientos, Prestación de Servicios y Administración de Bienes Muebles e Inmuebles del Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2016-11-12', 'reforma' => '2026-01-31'],
            ['titulo' => 'Ley de Transparencia, Acceso a la Información Pública con Sentido Social y Buen Gobierno del Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2025-12-31', 'reforma' => null],
            ['titulo' => 'Ley de Archivos para el Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2020-02-15', 'reforma' => '2024-02-10'],
            ['titulo' => 'Ley de Hacienda Municipal del Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '1990-09-01', 'reforma' => '2023-07-15'],
            ['titulo' => 'Ley de Coordinación Fiscal para el Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2008-12-29', 'reforma' => '2025-12-10'],
            ['titulo' => 'Ley de Fiscalización Superior y Rendición de Cuentas del Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2023-09-30', 'reforma' => '2025-07-09'],
            ['titulo' => 'Ley de Procedimientos y Justicia Administrativa para el Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2017-10-20', 'reforma' => '2025-09-06'],
            ['titulo' => 'Ley de Obras Públicas y Servicios Relacionados del Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2001-09-11', 'reforma' => '2025-12-08'],
            ['titulo' => 'Ley de Planeación, Desarrollo Administrativo y Servicios Públicos Municipales', 'medio' => $pogeo, 'publicacion' => '2011-04-02', 'reforma' => '2021-10-05'],
            ['titulo' => 'Ley de Protección de Datos Personales en Posesión de Sujetos Obligados del Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2025-12-31', 'reforma' => null],
            ['titulo' => 'Ley de Responsabilidades Administrativas del Estado y Municipios de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2017-10-03', 'reforma' => '2026-01-10'],
            ['titulo' => 'Ley Estatal de Presupuesto y Responsabilidad Hacendaria', 'medio' => $pogeo, 'publicacion' => '2011-12-24', 'reforma' => '2025-12-10'],
            ['titulo' => 'Ley General de Ingresos Municipales del Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2025-12-10', 'reforma' => null],
            ['titulo' => 'Código Penal para el Estado Libre y Soberano de Oaxaca', 'medio' => $pogeo, 'publicacion' => '1980-08-09', 'reforma' => '2026-05-23'],
            ['titulo' => 'Código de Procedimientos Civiles para el Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '1944-11-30', 'reforma' => '2021-03-13'],
            ['titulo' => 'Código de Procedimientos Penales para el Estado Libre y Soberano de Oaxaca', 'medio' => $pogeo, 'publicacion' => '1980-08-09', 'reforma' => '2016-01-30'],
            ['titulo' => 'Lineamientos para la Integración, Funcionamiento y Promoción de la Contraloría Social en el Estado de Oaxaca', 'medio' => $pogeo, 'publicacion' => '2018-10-06', 'reforma' => null],
        ];
    }

    /**
     * @return array<int, array{titulo: string, medio: string, publicacion: ?string, reforma: ?string}>
     */
    private function municipal(): array
    {
        $gaceta = 'Gaceta Municipal de Oaxaca de Juárez';
        $gacetaExtra = 'Gaceta Municipal Extra de Oaxaca de Juárez';
        $pogeo = 'Periódico Oficial del Gobierno del Estado de Oaxaca';

        return [
            ['titulo' => 'Bando de Policía y Gobierno del Municipio de Oaxaca de Juárez 2025-2027', 'medio' => $gaceta, 'publicacion' => '2025-01-01', 'reforma' => '2025-07-15'],
            ['titulo' => 'Plan Municipal de Desarrollo del Municipio de Oaxaca de Juárez 2025-2027', 'medio' => $gaceta, 'publicacion' => '2025-04-08', 'reforma' => null],
            ['titulo' => 'Código de Ética para las personas servidoras públicas del Municipio de Oaxaca de Juárez', 'medio' => $gaceta, 'publicacion' => '2025-08-26', 'reforma' => null],
            ['titulo' => 'Lineamientos del programa de mejora de la gestión pública del municipio de Oaxaca de Juárez', 'medio' => $gaceta, 'publicacion' => '2025-06-30', 'reforma' => null],
            ['titulo' => 'Lineamientos para la integración y operación del Comité de Ética del municipio de Oaxaca de Juárez, Oaxaca', 'medio' => $gaceta, 'publicacion' => '2025-09-09', 'reforma' => null],
            ['titulo' => 'Disposiciones del Sistema de Control Interno de la Administración Pública Municipal de Oaxaca de Juárez', 'medio' => $gacetaExtra, 'publicacion' => '2025-04-08', 'reforma' => null],
            ['titulo' => 'Lineamientos del Proceso de Entrega-Recepción de la Administración Pública del Municipio de Oaxaca de Juárez y de las Áreas Municipales', 'medio' => $gacetaExtra, 'publicacion' => '2018-06-25', 'reforma' => null],
            ['titulo' => 'Ley de Ingresos del Municipio de Oaxaca de Juárez, Oaxaca, para el Ejercicio Fiscal 2026', 'medio' => $pogeo, 'publicacion' => '2025-12-31', 'reforma' => null],
            ['titulo' => 'Presupuesto de Egresos del Municipio de Oaxaca de Juárez, Oaxaca para el ejercicio 2026', 'medio' => $gaceta, 'publicacion' => '2025-12-01', 'reforma' => null],
        ];
    }
}
