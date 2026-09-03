<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Direccion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'direcciones';

    protected $fillable = [
        'clave',
        'nombre',
        'siglas',
        'descripcion',
        'orden',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
            'orden' => 'integer',
        ];
    }

    /**
     * Documentos clasificados bajo esta dirección.
     *
     * @return HasMany<Documento, $this>
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * Páginas institucionales de esta dirección (procesos, oficios, formatos propios).
     *
     * @return HasMany<PaginaInstitucional, $this>
     */
    public function paginas(): HasMany
    {
        return $this->hasMany(PaginaInstitucional::class);
    }

    /**
     * Contactos asociados a esta dirección.
     *
     * @return HasMany<Contacto, $this>
     */
    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class);
    }

    /**
     * Departamentos que integran esta dirección (organigrama de la Fase 7).
     *
     * @return HasMany<Departamento, $this>
     */
    public function departamentos(): HasMany
    {
        return $this->hasMany(Departamento::class);
    }

    /**
     * Filtra únicamente las direcciones activas, ordenadas para su listado público.
     *
     * @param  Builder<Direccion>  $query
     * @return Builder<Direccion>
     */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true)->orderBy('orden');
    }
}
