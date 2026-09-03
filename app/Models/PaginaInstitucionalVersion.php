<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaginaInstitucionalVersion extends Model
{
    use HasFactory;

    protected $table = 'pagina_institucional_versiones';

    protected $fillable = [
        'pagina_institucional_id',
        'numero_version',
        'titulo',
        'contenido',
        'actualizado_por',
    ];

    protected function casts(): array
    {
        return [
            'numero_version' => 'integer',
        ];
    }

    /**
     * Página institucional vigente a la que pertenece esta versión histórica.
     *
     * @return BelongsTo<PaginaInstitucional, $this>
     */
    public function pagina(): BelongsTo
    {
        return $this->belongsTo(PaginaInstitucional::class, 'pagina_institucional_id');
    }

    /**
     * Usuario que guardó el contenido archivado en esta versión.
     *
     * @return BelongsTo<User, $this>
     */
    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }
}
