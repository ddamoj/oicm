<?php

namespace App\Services;

use App\Models\PaginaInstitucional;
use App\Models\PaginaInstitucionalVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Editor de contenido institucional con bloques versionados (Fase 7): antes
 * de sobrescribir una página institucional, archiva su estado vigente en
 * `pagina_institucional_versiones` — mismo patrón que `AlmacenDocumentos`
 * usa para el reemplazo de archivos (Fase 4).
 */
class VersionadorPaginas
{
    /**
     * @param  array{titulo: string, contenido: string, estatus: string}  $datosNuevos
     */
    public function guardar(PaginaInstitucional $pagina, array $datosNuevos, User $usuario): PaginaInstitucional
    {
        DB::transaction(function () use ($pagina, $datosNuevos, $usuario) {
            $siguienteVersion = (int) $pagina->versiones()->max('numero_version') + 1;

            PaginaInstitucionalVersion::query()->create([
                'pagina_institucional_id' => $pagina->id,
                'numero_version' => $siguienteVersion,
                'titulo' => $pagina->titulo,
                'contenido' => $pagina->contenido,
                'actualizado_por' => $usuario->id,
            ]);

            $pagina->update([
                ...$datosNuevos,
                'actualizado_por' => $usuario->id,
            ]);
        });

        return $pagina->fresh();
    }

    /**
     * Restaura una versión archivada como el contenido vigente, archivando a
     * su vez el estado actual antes de sobrescribirlo (nunca se pierde nada).
     */
    public function restaurar(PaginaInstitucional $pagina, PaginaInstitucionalVersion $version, User $usuario): PaginaInstitucional
    {
        return $this->guardar($pagina, [
            'titulo' => $version->titulo,
            'contenido' => $version->contenido,
        ], $usuario);
    }
}
