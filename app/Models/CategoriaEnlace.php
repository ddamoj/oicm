<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaEnlace extends Model
{
    use HasFactory;

    protected $table = 'categorias_enlace';

    protected $fillable = [
        'clave',
        'nombre',
        'orden',
    ];

    /**
     * Enlaces que pertenecen a esta categoría.
     *
     * @return HasMany<Enlace, $this>
     */
    public function enlaces(): HasMany
    {
        return $this->hasMany(Enlace::class, 'categoria_enlace_id');
    }
}
