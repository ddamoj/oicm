<?php

namespace App\Models;

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
    ];

    /**
     * Dirección a la que pertenece este contacto (puede ser nula para contacto general).
     *
     * @return BelongsTo<Direccion, $this>
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }
}
