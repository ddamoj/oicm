<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriaMedio extends Model
{
    use HasFactory;

    protected $fillable = [
        'galeria_id',
        'tipo',
        'ruta_archivo',
        'descripcion_alt',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
        ];
    }

    /**
     * Galería a la que pertenece este medio.
     *
     * @return BelongsTo<Galeria, $this>
     */
    public function galeria(): BelongsTo
    {
        return $this->belongsTo(Galeria::class);
    }
}
