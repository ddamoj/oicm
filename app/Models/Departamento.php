<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'direccion_id',
        'nombre',
        'siglas',
        'descripcion',
        'orden',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }

    /**
     * Dirección de área a la que pertenece este departamento.
     *
     * @return BelongsTo<Direccion, $this>
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }

    /**
     * Filtra únicamente los departamentos activos, ordenados para el organigrama público.
     *
     * @param  Builder<Departamento>  $query
     * @return Builder<Departamento>
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('orden');
    }
}
