<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Normatividad extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'normatividad';

    protected $fillable = [
        'ambito',
        'titulo',
        'descripcion',
        'medio_publicacion',
        'fecha_publicacion',
        'fecha_ultima_reforma',
        'documento_url',
        'orden',
        'vigente',
    ];

    protected function casts(): array
    {
        return [
            'fecha_publicacion' => 'date',
            'fecha_ultima_reforma' => 'date',
            'vigente' => 'boolean',
            'orden' => 'integer',
        ];
    }

    /**
     * Filtra únicamente la normatividad vigente.
     *
     * @param  Builder<Normatividad>  $query
     * @return Builder<Normatividad>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('vigente', true)->orderBy('orden');
    }

    /**
     * Filtra por ámbito: federal, estatal o municipal.
     *
     * @param  Builder<Normatividad>  $query
     * @return Builder<Normatividad>
     */
    public function scopePorAmbito(Builder $query, string $ambito): Builder
    {
        return $query->where('ambito', $ambito);
    }
}
