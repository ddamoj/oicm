<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Noticia extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'slug',
        'contenido',
        'imagen_portada',
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
     * Autor (usuario administrativo) que redactó la noticia.
     *
     * @return BelongsTo<User, $this>
     */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    /**
     * Filtra únicamente las noticias publicadas, de la más reciente a la más antigua.
     *
     * @param  Builder<Noticia>  $query
     * @return Builder<Noticia>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('estatus', 'publicada')
            ->whereNotNull('publicado_en')
            ->orderByDesc('publicado_en');
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
