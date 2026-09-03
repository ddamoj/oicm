<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Noticia extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'slug',
        'contenido',
        'resumen',
        'imagen_portada',
        'imagen_alt',
        'imagen_miniatura',
        'estatus',
        'publicado_en',
        'autor_id',
    ];

    protected function casts(): array
    {
        return [
            'publicado_en' => 'datetime',
        ];
    }

    /**
     * Las noticias se resuelven por slug en las rutas públicas (URLs legibles).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * URL pública de la imagen de portada, o null si la noticia no tiene una.
     */
    public function urlImagen(): ?string
    {
        return $this->imagen_portada ? Storage::disk('public')->url($this->imagen_portada) : null;
    }

    /**
     * URL pública de la miniatura, con la portada como respaldo si aún no existe.
     */
    public function urlMiniatura(): ?string
    {
        if ($this->imagen_miniatura) {
            return Storage::disk('public')->url($this->imagen_miniatura);
        }

        return $this->urlImagen();
    }

    /**
     * Autor (usuario administrativo) que redactó la noticia.
     *
     * @return BelongsTo<User, $this>
     */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    /**
     * Filtra únicamente las noticias publicadas y cuya fecha de publicación ya
     * llegó (una noticia "programada" con fecha futura no es aún visible),
     * de la más reciente a la más antigua.
     *
     * @param  Builder<Noticia>  $query
     * @return Builder<Noticia>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('estatus', 'publicada')
            ->whereNotNull('publicado_en')
            ->where('publicado_en', '<=', now())
            ->orderByDesc('publicado_en');
    }

    /**
     * Búsqueda por palabra clave sobre título, resumen y contenido (RF-NOT-003).
     *
     * @param  Builder<Noticia>  $query
     * @return Builder<Noticia>
     */
    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        $comodin = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $termino).'%';

        return $query->where(function (Builder $consulta) use ($comodin) {
            $consulta->where('titulo', 'like', $comodin)
                ->orWhere('resumen', 'like', $comodin)
                ->orWhere('contenido', 'like', $comodin);
        });
    }

    /**
     * Filtra noticias por rango de fechas de publicación (RF-NOT-003).
     *
     * @param  Builder<Noticia>  $query
     * @return Builder<Noticia>
     */
    public function scopeEntreFechas(Builder $query, ?string $desde, ?string $hasta): Builder
    {
        return $query
            ->when($desde, fn (Builder $q) => $q->whereDate('publicado_en', '>=', $desde))
            ->when($hasta, fn (Builder $q) => $q->whereDate('publicado_en', '<=', $hasta));
    }
}
