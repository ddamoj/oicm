<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contacto extends Model
{
    use HasFactory;

    protected $fillable = [
        'direccion_id',
        'nombre_area',
        'domicilio',
        'telefono',
        'correo',
        'horario',
        'latitud',
        'longitud',
        'canal_quejas_denuncias',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'orden' => 'integer',
        ];
    }

    /**
     * Dirección a la que pertenece este contacto (puede ser nula para contacto general).
     *
     * @return BelongsTo<Direccion, $this>
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }

    /**
     * Orden estable del directorio público.
     *
     * @param  Builder<Contacto>  $query
     * @return Builder<Contacto>
     */
    public function scopeOrdenado(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('nombre_area');
    }

    /**
     * True si tiene coordenadas válidas para embeber el mapa.
     */
    public function tieneMapa(): bool
    {
        return $this->latitud !== null && $this->longitud !== null;
    }
}
