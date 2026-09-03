<?php

namespace App\Http\Controllers;

use App\Models\Estrado;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Descarga pública de un estrado digital, sin autenticación. Igual que
 * `DocumentoDescargaController` (Fase 4), es el único punto por el que se
 * sirve el archivo del disco privado "documentos": nunca se expone por URL
 * directa, lo que evita path traversal y ejecución.
 */
class EstradoDescargaController extends Controller
{
    public function __invoke(Estrado $estrado): StreamedResponse|Response
    {
        try {
            // Un estrado retirado o eliminado no debe poder descargarse aunque
            // se conozca su identificador.
            if (! $estrado->activo) {
                abort(404);
            }

            if (! Storage::disk('documentos')->exists($estrado->archivo_ruta)) {
                Log::error('Archivo de estrado no encontrado en disco.', ['estrado_id' => $estrado->id]);
                abort(404);
            }

            return Storage::disk('documentos')->download(
                $estrado->archivo_ruta,
                $estrado->archivo_nombre,
                ['X-Content-Type-Options' => 'nosniff']
            );
        } catch (HttpException $excepcion) {
            throw $excepcion;
        } catch (\Throwable $excepcion) {
            Log::error('Error al descargar un estrado.', ['estrado_id' => $estrado->id, 'error' => $excepcion->getMessage()]);
            abort(500);
        }
    }
}
