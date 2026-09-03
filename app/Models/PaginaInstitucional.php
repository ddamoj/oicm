<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaginaInstitucional extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'paginas_institucionales';

    protected $fillable = [
        'direccion_id',
        'slug',
        'titulo',
        'contenido',
        'estatus',
        'actualizado_por',
    ];

    /**
     * Dirección a la que pertenece esta página (puede ser nula para páginas globales).
     *
     * @return BelongsTo<Direccion, $this>
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class);
    }

    /**
     * Usuario que realizó la última actualización de contenido.
     *
     * @return BelongsTo<User, $this>
     */
    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    /**
     * Historial de versiones archivadas de esta página (editor de bloques versionados).
     *
     * @return HasMany<PaginaInstitucionalVersion, $this>
     */
    public function versiones(): HasMany
    {
        return $this->hasMany(PaginaInstitucionalVersion::class)->orderByDesc('numero_version');
    }

    /**
     * Filtra únicamente las páginas publicadas, para consumo público.
     *
     * @param  Builder<PaginaInstitucional>  $query
     * @return Builder<PaginaInstitucional>
     */
    public function scopePublicado(Builder $query): Builder
    {
        return $query->where('estatus', 'publicada');
    }

    /**
     * Busca por título o contenido (buscador global, Fase 8).
     *
     * @param  Builder<PaginaInstitucional>  $query
     * @return Builder<PaginaInstitucional>
     */
    public function scopeBuscar(Builder $query, string $palabraClave): Builder
    {
        return $query->where(function (Builder $q) use ($palabraClave) {
            $q->where('titulo', 'like', "%{$palabraClave}%")
                ->orWhere('contenido', 'like', "%{$palabraClave}%");
        });
    }

    /**
     * URL pública de la página: "Quiénes somos" o la ficha de su Dirección.
     * Null-safety: si no encaja en ninguno de los dos casos, apunta al inicio.
     */
    public function urlPublica(): string
    {
        try {
            if ($this->slug === 'quienes-somos' && $this->direccion_id === null) {
                return route('quienes-somos');
            }

            if ($this->direccion) {
                return route('direcciones.mostrar', $this->direccion->clave);
            }

            return route('inicio');
        } catch (\Throwable) {
            return route('inicio');
        }
    }
}
