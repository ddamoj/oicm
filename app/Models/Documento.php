<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'categoria_documento_id',
        'direccion_id',
        'nombre',
        'descripcion',
        'ruta_archivo',
        'nombre_original',
        'extension',
        'mime_type',
        'tamano_bytes',
        'contador_descargas',
        'publicado',
        'subido_por',
    ];

    protected function casts(): array
    {
        return [
            'publicado' => 'boolean',
            'tamano_bytes' => 'integer',
            'contador_descargas' => 'integer',
        ];
    }

    /**
     * Categoría (Formatos, Oficios, Bases de Datos) a la que pertenece el documento.
     *
     * @return BelongsTo<CategoriaDocumento, $this>
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaDocumento::class, 'categoria_documento_id');
    }

    /**
     * Dirección responsable del documento.
     *
     * @return BelongsTo<Direccion, $this>
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }

    /**
     * Usuario que realizó la carga original del documento.
     *
     * @return BelongsTo<User, $this>
     */
    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    /**
     * Historial de versiones anteriores conservadas al reemplazar el archivo (RF-CAR-002).
     *
     * @return HasMany<DocumentoVersion, $this>
     */
    public function versiones(): HasMany
    {
        return $this->hasMany(DocumentoVersion::class);
    }

    /**
     * Filtra únicamente los documentos publicados, visibles sin autenticación (RF-DES-001).
     *
     * @param  Builder<Documento>  $query
     * @return Builder<Documento>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('publicado', true);
    }

    /**
     * Filtra documentos por categoría (RF-DES-002).
     *
     * @param  Builder<Documento>  $query
     * @return Builder<Documento>
     */
    public function scopePorCategoria(Builder $query, int $categoriaDocumentoId): Builder
    {
        return $query->where('categoria_documento_id', $categoriaDocumentoId);
    }

    /**
     * Filtra documentos por dirección responsable (Fase 7: enlace desde la
     * ficha de cada Dirección al repositorio de documentos).
     *
     * @param  Builder<Documento>  $query
     * @return Builder<Documento>
     */
    public function scopePorDireccion(Builder $query, int $direccionId): Builder
    {
        return $query->where('direccion_id', $direccionId);
    }

    /**
     * Búsqueda de documentos por palabra clave en nombre o descripción (RF-DES-002).
     *
     * @param  Builder<Documento>  $query
     * @return Builder<Documento>
     */
    public function scopeBuscar(Builder $query, string $palabraClave): Builder
    {
        return $query->where(function (Builder $q) use ($palabraClave) {
            $q->where('nombre', 'like', "%{$palabraClave}%")
                ->orWhere('descripcion', 'like', "%{$palabraClave}%");
        });
    }
}
