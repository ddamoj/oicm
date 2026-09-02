<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galeria extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_evento',
        'publicada',
    ];

    protected function casts(): array
    {
        return [
            'fecha_evento' => 'date',
            'publicada' => 'boolean',
        ];
    }

    /**
     * Fotos y videos que integran esta galería.
     *
     * @return HasMany<GaleriaMedio, $this>
     */
    public function medios(): HasMany
    {
        return $this->hasMany(GaleriaMedio::class);
    }

    /**
     * Filtra únicamente las galerías publicadas.
     *
     * @param  Builder<Galeria>  $query
     * @return Builder<Galeria>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('publicada', true)->orderByDesc('fecha_evento');
    }
}
