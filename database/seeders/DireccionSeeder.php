<?php

namespace Database\Seeders;

use App\Models\Direccion;
use Illuminate\Database\Seeder;

class DireccionSeeder extends Seeder
{
    /**
     * Carga la Oficina del Contralor y las cuatro direcciones de área del OICM
     * (tomado del documento de carga de información institucional).
     */
    public function run(): void
    {
        $direcciones = [
            [
                'clave' => 'oficina-contralor',
                'nombre' => 'Oficina del Contralor',
                'siglas' => null,
                'descripcion' => 'Titularidad del Órgano Interno de Control Municipal.',
                'orden' => 1,
            ],
            [
                'clave' => 'auditoria-interna',
                'nombre' => 'Dirección de Auditoría Interna',
                'siglas' => 'DAI',
                'descripcion' => 'Realiza auditorías, revisiones y acciones de control sobre la gestión pública municipal.',
                'orden' => 2,
            ],
            [
                'clave' => 'control-mejora-gestion',
                'nombre' => 'Dirección de Control y Mejora de la Gestión Pública Municipal',
                'siglas' => 'DCyMGPM',
                'descripcion' => 'Orientada a la prevención de riesgos, la implementación del control interno y la mejora de procesos.',
                'orden' => 3,
            ],
            [
                'clave' => 'quejas-denuncias-patrimonial',
                'nombre' => 'Dirección de Quejas, Denuncias, Investigación y Situación Patrimonial',
                'siglas' => 'DQDISP',
                'descripcion' => 'Atiende quejas, denuncias e investigaciones, y da seguimiento a la situación patrimonial.',
                'orden' => 4,
            ],
            [
                'clave' => 'responsabilidades-controversias-sanciones',
                'nombre' => 'Dirección de Responsabilidades Administrativas, Controversias y Sanciones',
                'siglas' => 'DRACS',
                'descripcion' => 'Substancia procedimientos de responsabilidad administrativa y opera los estrados digitales.',
                'orden' => 5,
            ],
        ];

        foreach ($direcciones as $direccion) {
            Direccion::query()->updateOrCreate(['clave' => $direccion['clave']], $direccion);
        }
    }
}
