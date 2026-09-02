<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitacoraAuditoria extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public const CREATED_AT = 'creado_en';

    protected $table = 'bitacora_auditoria';

    protected $fillable = [
        'user_id',
        'accion',
        'modelo_afectado',
        'modelo_id',
        'detalle',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'detalle' => 'array',
            'creado_en' => 'datetime',
        ];
    }

    /**
     * Usuario que realizó la acción registrada (puede ser nulo si el usuario fue eliminado).
     *
     * @return BelongsTo<User, $this>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Registra una entrada de auditoría de forma segura, sin interrumpir el flujo
     * principal si el registro falla (la auditoría nunca debe romper la operación).
     *
     * @param  array<string, mixed>|null  $detalle
     */
    public static function registrar(string $accion, ?string $modeloAfectado = null, ?int $modeloId = null, ?array $detalle = null): void
    {
        try {
            static::create([
                'user_id' => auth()->id(),
                'accion' => $accion,
                'modelo_afectado' => $modeloAfectado,
                'modelo_id' => $modeloId,
                'detalle' => $detalle,
                'ip' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        } catch (\Throwable) {
            // La bitácora no debe interrumpir la operación principal si falla el registro.
        }
    }
}
