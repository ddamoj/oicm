<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enlace extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'categoria_enlace_id',
        'nombre',
        'descripcion',
        'url',
        'orden',
        'activo',
        'destacado_inicio',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden' => 'integer',
            'destacado_inicio' => 'boolean',
        ];
    }

    /**
     * Categoría a la que pertenece el enlace.
     *
     * @return BelongsTo<CategoriaEnlace, $this>
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaEnlace::class, 'categoria_enlace_id');
    }

    /**
     * Filtra únicamente los enlaces activos, visibles en el directorio público.
     *
     * @param  Builder<Enlace>  $query
     * @return Builder<Enlace>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    /**
     * Filtra enlaces por categoría.
     *
     * @param  Builder<Enlace>  $query
     * @return Builder<Enlace>
     */
    public function scopePorCategoria(Builder $query, int $categoriaEnlaceId): Builder
    {
        return $query->where('categoria_enlace_id', $categoriaEnlaceId);
    }

    /**
     * Busca por nombre, descripción o URL.
     *
     * @param  Builder<Enlace>  $query
     * @return Builder<Enlace>
     */
    public function scopeBuscar(Builder $query, string $palabraClave): Builder
    {
        return $query->where(function (Builder $q) use ($palabraClave) {
            $q->where('nombre', 'like', "%{$palabraClave}%")
                ->orWhere('descripcion', 'like', "%{$palabraClave}%")
                ->orWhere('url', 'like', "%{$palabraClave}%");
        });
    }
}
