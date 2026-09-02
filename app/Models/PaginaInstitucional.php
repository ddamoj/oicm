<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaginaInstitucional extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'paginas_institucionales';

    protected $fillable = [
        'direccion_id',
        'slug',
        'titulo',
        'contenido',
        'estatus',
        'actualizado_por',
    ];

    /**
     * Dirección a la que pertenece esta página (puede ser nula para páginas globales).
     *
     * @return BelongsTo<Direccion, $this>
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }

    /**
     * Usuario que realizó la última actualización de contenido.
     *
     * @return BelongsTo<User, $this>
     */
    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    /**
     * Filtra únicamente las páginas publicadas, para consumo público.
     *
     * @param  Builder<PaginaInstitucional>  $query
     * @return Builder<PaginaInstitucional>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('estatus', 'publicada');
    }
}
