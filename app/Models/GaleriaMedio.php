<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GaleriaMedio extends Model
{
    use HasFactory;

    protected $fillable = [
        'galeria_id',
        'tipo',
        'ruta_archivo',
        'url_externa',
        'ruta_miniatura',
        'descripcion_alt',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
        ];
    }

    /**
     * Galería a la que pertenece este medio.
     *
     * @return BelongsTo<Galeria, $this>
     */
    public function galeria(): BelongsTo
    {
        return $this->belongsTo(Galeria::class);
    }

    /**
     * URL pública del archivo subido (foto o video), o null si el medio es
     * una URL externa. Null-safety: nunca lanza si ruta_archivo está vacío.
     */
    public function urlArchivo(): ?string
    {
        try {
            return $this->ruta_archivo ? Storage::disk('public')->url($this->ruta_archivo) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * URL de la miniatura (fotos) para el grid público; para video subido o
     * externo sin miniatura propia, la vista usa un marcador genérico.
     */
    public function urlMiniatura(): ?string
    {
        try {
            $ruta = $this->ruta_miniatura ?: $this->ruta_archivo;

            return $ruta ? Storage::disk('public')->url($ruta) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Extrae proveedor e ID de una URL externa de YouTube/Vimeo mediante una
     * lista blanca de patrones, para construir el iframe de incrustación en
     * el servidor sin exponer nunca la URL cruda del usuario en el HTML
     * (evita inyección vía atributo src).
     *
     * @return array{proveedor: string, id: string}|null
     */
    public function proveedorVideoExterno(): ?array
    {
        if (! $this->url_externa) {
            return null;
        }

        if (preg_match('/^https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{6,20})/', $this->url_externa, $coincidencia)) {
            return ['proveedor' => 'youtube', 'id' => $coincidencia[1]];
        }

        if (preg_match('/^https?:\/\/(?:www\.)?vimeo\.com\/(\d+)/', $this->url_externa, $coincidencia)) {
            return ['proveedor' => 'vimeo', 'id' => $coincidencia[1]];
        }

        return null;
    }

    /**
     * Estructura plana para el lightbox público (Alpine, vía JSON): nunca
     * incluye la URL externa cruda, solo el proveedor y el ID ya validados
     * por proveedorVideoExterno(), para que el iframe se construya siempre
     * en el servidor a partir de una lista blanca.
     *
     * @return array{id: int, tipo: string, alt: string, urlGrande: ?string, urlMiniatura: ?string, proveedorExterno: ?array{proveedor: string, id: string}}
     */
    public function datosLightbox(): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'alt' => (string) $this->descripcion_alt,
            'urlGrande' => $this->urlArchivo(),
            'urlMiniatura' => $this->urlMiniatura(),
            'proveedorExterno' => $this->proveedorVideoExterno(),
        ];
    }
}
