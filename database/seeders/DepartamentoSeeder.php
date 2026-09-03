<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\Direccion;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Los 11 departamentos del organigrama del OICM ("un Contralor, cuatro
     * direcciones de área y 11 departamentos", según
     * `INFORMACIÓN PARA LA ATD PARA SU CARGA EN EL PORTAL DEL OICM.pdf`, p.9).
     *
     * PENDIENTE DE CONFIRMACIÓN: el documento de carga no trae los nombres de
     * los departamentos (el organigrama llega como imagen, no como texto). Los
     * nombres de abajo son tentativos, derivados de las funciones descritas
     * para cada Dirección en el mismo documento, y deben sustituirse por los
     * oficiales en cuanto el cliente los entregue — mismo tratamiento que las
     * URL pendientes de la Fase 6 (ver `context/plandetrabajo.md`).
     */
    public function run(): void
    {
        $porDireccion = [
            'auditoria-interna' => [
                'Departamento de Auditoría Financiera y Contable',
                'Departamento de Auditoría a Obra Pública',
                'Departamento de Seguimiento de Observaciones',
            ],
            'control-mejora-gestion' => [
                'Departamento de Control Interno',
                'Departamento de Mejora de Procesos',
                'Departamento de Evaluación de la Gestión Pública',
            ],
            'quejas-denuncias-patrimonial' => [
                'Departamento de Quejas y Denuncias',
                'Departamento de Situación Patrimonial e Intereses',
            ],
            'responsabilidades-controversias-sanciones' => [
                'Departamento de Substanciación',
                'Departamento de Resolución y Sanciones',
                'Departamento de Estrados y Notificaciones',
            ],
        ];

        foreach ($porDireccion as $claveDireccion => $departamentos) {
            $direccion = Direccion::query()->where('clave', $claveDireccion)->first();

            if (! $direccion) {
                continue;
            }

            foreach ($departamentos as $orden => $nombre) {
                Departamento::query()->updateOrCreate(
                    ['direccion_id' => $direccion->id, 'nombre' => $nombre],
                    ['orden' => $orden + 1, 'activo' => true]
                );
            }
        }
    }
}
