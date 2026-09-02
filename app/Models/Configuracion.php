<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    /**
     * Obtiene el valor de una configuración por su clave, con un valor por defecto
     * seguro cuando la clave no existe (null-safety).
     */
    public static function obtener(string $clave, ?string $porDefecto = null): ?string
    {
        try {
            return static::query()->where('clave', $clave)->first()?->valor ?? $porDefecto;
        } catch (\Throwable) {
            return $porDefecto;
        }
    }
}
