<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Direccion;
use Illuminate\Database\Seeder;

class ContactoSeeder extends Seeder
{
    /**
     * Directorio de contacto (Fase 8): domicilio institucional del Palacio
     * Municipal para las 5 Direcciones del OICM y un contacto general.
     * Coordenadas del Zócalo de Oaxaca de Juárez (ubicación del Palacio
     * Municipal); teléfono/correo específicos por Dirección quedan
     * pendientes de confirmación por el cliente, igual que los nombres de
     * los departamentos de la Fase 7.
     */
    private const LATITUD_PALACIO_MUNICIPAL = 17.0654100;

    private const LONGITUD_PALACIO_MUNICIPAL = -96.7236500;

    public function run(): void
    {
        $porClave = fn (string $clave) => Direccion::query()->where('clave', $clave)->first()?->id;

        $contactos = [
            [
                'nombre_area' => 'Contacto general del OICM',
                'direccion_id' => null,
                'orden' => 0,
                'canal_quejas_denuncias' => null,
            ],
            [
                'nombre_area' => 'Oficina del Contralor',
                'direccion_id' => $porClave('oficina-contralor'),
                'orden' => 1,
                'canal_quejas_denuncias' => null,
            ],
            [
                'nombre_area' => 'Dirección de Auditoría Interna',
                'direccion_id' => $porClave('auditoria-interna'),
                'orden' => 2,
                'canal_quejas_denuncias' => null,
            ],
            [
                'nombre_area' => 'Dirección de Control y Mejora de la Gestión Pública Municipal',
                'direccion_id' => $porClave('control-mejora-gestion'),
                'orden' => 3,
                'canal_quejas_denuncias' => null,
            ],
            [
                'nombre_area' => 'Dirección de Quejas, Denuncias, Investigación y Situación Patrimonial',
                'direccion_id' => $porClave('quejas-denuncias-patrimonial'),
                'orden' => 4,
                'canal_quejas_denuncias' => 'contraloria@municipiodeoaxaca.gob.mx',
            ],
            [
                'nombre_area' => 'Dirección de Responsabilidades Administrativas, Controversias y Sanciones',
                'direccion_id' => $porClave('responsabilidades-controversias-sanciones'),
                'orden' => 5,
                'canal_quejas_denuncias' => null,
            ],
        ];

        foreach ($contactos as $contacto) {
            Contacto::query()->updateOrCreate(
                ['nombre_area' => $contacto['nombre_area']],
                [
                    'direccion_id' => $contacto['direccion_id'],
                    'domicilio' => 'Palacio Municipal, Portal del Palacio s/n, Centro, Oaxaca de Juárez, Oax.',
                    'telefono' => null,
                    'correo' => $contacto['direccion_id'] === null ? 'contraloria@municipiodeoaxaca.gob.mx' : null,
                    'horario' => 'Lunes a viernes, 9:00 a 17:00 h',
                    'latitud' => self::LATITUD_PALACIO_MUNICIPAL,
                    'longitud' => self::LONGITUD_PALACIO_MUNICIPAL,
                    'canal_quejas_denuncias' => $contacto['canal_quejas_denuncias'],
                    'orden' => $contacto['orden'],
                ]
            );
        }
    }
}
