<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Descarga pública de documentos, sin autenticación (RF-DES-001). Es el único
 * punto por el que se sirve un archivo del disco privado "documentos": nunca
 * se expone por URL directa, lo que evita path traversal y ejecución.
 */
class DocumentoDescargaController extends Controller
{
    public function __invoke(Documento $documento): StreamedResponse|Response
    {
        try {
            // Un documento no publicado o eliminado no debe poder descargarse
            // aunque se conozca su identificador.
            if (! $documento->publicado) {
                abort(404);
            }

            if (! Storage::disk('documentos')->exists($documento->ruta_archivo)) {
                Log::error('Archivo de documento no encontrado en disco.', ['documento_id' => $documento->id]);
                abort(404);
            }

            // Incremento atómico: evita condiciones de carrera entre descargas
            // concurrentes y no altera el "actualizado el" del documento.
            $documento->increment('contador_descargas');

            return Storage::disk('documentos')->download(
                $documento->ruta_archivo,
                $documento->nombre_original,
                ['X-Content-Type-Options' => 'nosniff']
            );
        } catch (HttpException $excepcion) {
            throw $excepcion;
        } catch (\Throwable $excepcion) {
            Log::error('Error al descargar un documento.', ['documento_id' => $documento->id, 'error' => $excepcion->getMessage()]);
            abort(500);
        }
    }
}
