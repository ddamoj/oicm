<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaDocumento extends Model
{
    use HasFactory;

    protected $table = 'categorias_documento';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'orden',
    ];

    /**
     * Documentos que pertenecen a esta categoría.
     *
     * @return HasMany<Documento, $this>
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'categoria_documento_id');
    }
}
