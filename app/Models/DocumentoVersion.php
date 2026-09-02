<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoVersion extends Model
{
    use HasFactory;

    protected $table = 'documento_versiones';

    protected $fillable = [
        'documento_id',
        'numero_version',
        'ruta_archivo',
        'nombre_original',
        'extension',
        'mime_type',
        'tamano_bytes',
        'reemplazado_por',
    ];

    protected function casts(): array
    {
        return [
            'numero_version' => 'integer',
            'tamano_bytes' => 'integer',
        ];
    }

    /**
     * Documento vigente al que pertenece esta versión histórica.
     *
     * @return BelongsTo<Documento, $this>
     */
    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class);
    }

    /**
     * Usuario que reemplazó el archivo generando esta versión.
     *
     * @return BelongsTo<User, $this>
     */
    public function reemplazadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reemplazado_por');
    }
}
