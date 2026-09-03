<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estrado extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'numero',
        'expediente',
        'asunto',
        'fecha_publicacion',
        'archivo_ruta',
        'archivo_nombre',
        'archivo_extension',
        'archivo_mime',
        'archivo_tamano',
        'archivo_hash',
        'datos_testados',
        'publicado_por',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_publicacion' => 'datetime',
            'numero' => 'integer',
            'archivo_tamano' => 'integer',
            'datos_testados' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    /**
     * Usuario que publicó este estrado digital.
     *
     * @return BelongsTo<User, $this>
     */
    public function publicadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publicado_por');
    }

    /**
     * Filtra únicamente los estrados activos, visibles sin autenticación.
     *
     * @param  Builder<Estrado>  $query
     * @return Builder<Estrado>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * Búsqueda por número, expediente o asunto.
     *
     * @param  Builder<Estrado>  $query
     * @return Builder<Estrado>
     */
    public function scopeBuscar(Builder $query, string $palabraClave): Builder
    {
        return $query->where(function (Builder $q) use ($palabraClave) {
            $q->where('asunto', 'like', "%{$palabraClave}%")
                ->orWhere('expediente', 'like', "%{$palabraClave}%")
                ->orWhere('numero', 'like', "%{$palabraClave}%");
        });
    }
}
