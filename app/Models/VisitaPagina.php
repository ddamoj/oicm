<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Visita a una página pública del micrositio.
 *
 * Analítica propia y mínima, sin servicios de terceros ni cookies: la ERS
 * pide conocer el uso del micrositio, no identificar a quien lo consulta. De
 * la petición solo se conservan la ruta, el tipo de dispositivo y una huella
 * irreversible de la IP.
 */
class VisitaPagina extends Model
{
    use HasFactory;

    protected $table = 'visitas_pagina';

    /** La tabla solo necesita el instante de la visita, no created/updated. */
    public $timestamps = false;

    protected $fillable = [
        'ruta',
        'ip_huella',
        'dispositivo',
        'visitada_en',
    ];

    protected function casts(): array
    {
        return [
            'visitada_en' => 'datetime',
        ];
    }

    /**
     * Visitas registradas hoy.
     *
     * @param  Builder<VisitaPagina>  $query
     * @return Builder<VisitaPagina>
     */
    public function scopeHoy(Builder $query): Builder
    {
        return $query->whereDate('visitada_en', today());
    }

    /**
     * Visitas de la semana en curso (lunes a domingo).
     *
     * @param  Builder<VisitaPagina>  $query
     * @return Builder<VisitaPagina>
     */
    public function scopeEstaSemana(Builder $query): Builder
    {
        return $query->whereBetween('visitada_en', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Visitas del mes en curso.
     *
     * @param  Builder<VisitaPagina>  $query
     * @return Builder<VisitaPagina>
     */
    public function scopeEsteMes(Builder $query): Builder
    {
        return $query->whereYear('visitada_en', now()->year)
            ->whereMonth('visitada_en', now()->month);
    }

    /**
     * Visitas desde el inicio del día que quedó hace `$dias - 1` jornadas,
     * de modo que "7 días" incluya hoy y los seis anteriores completos.
     *
     * @param  Builder<VisitaPagina>  $query
     * @return Builder<VisitaPagina>
     */
    public function scopeUltimosDias(Builder $query, int $dias): Builder
    {
        return $query->where('visitada_en', '>=', now()->subDays(max($dias, 1) - 1)->startOfDay());
    }

    /**
     * Visitas de una ruta concreta.
     *
     * @param  Builder<VisitaPagina>  $query
     * @return Builder<VisitaPagina>
     */
    public function scopePorRuta(Builder $query, string $ruta): Builder
    {
        return $query->where('ruta', $ruta);
    }

    /**
     * Registra una visita a partir de la petición. Nunca lanza: la analítica
     * no debe tumbar la página que se está sirviendo.
     */
    public static function registrar(Request $peticion, string $ruta): void
    {
        try {
            $agente = $peticion->userAgent() ?? '';

            if (self::esRobot($agente)) {
                return;
            }

            self::create([
                'ruta' => self::normalizarRuta($ruta),
                'ip_huella' => self::huellaDe($peticion->ip() ?? ''),
                'dispositivo' => self::detectarDispositivo($agente),
                'visitada_en' => now(),
            ]);
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible registrar la visita.', ['error' => $excepcion->getMessage()]);
        }
    }

    /**
     * Huella irreversible de la IP. Se usa HMAC con `APP_KEY` en vez de un
     * SHA-256 a secas: el espacio de direcciones IPv4 es lo bastante pequeño
     * (2^32) como para revertir un hash sin sal por fuerza bruta en minutos,
     * de modo que un hash simple no anonimiza de verdad.
     */
    public static function huellaDe(string $ip): string
    {
        return hash_hmac('sha256', $ip, (string) config('app.key'));
    }

    /**
     * Ruta normalizada con una sola barra inicial, para que `/noticias` y
     * `noticias` no se cuenten como dos páginas distintas.
     */
    private static function normalizarRuta(string $ruta): string
    {
        return '/'.ltrim($ruta, '/');
    }

    /**
     * Descarta rastreadores automáticos: sin esto, el tablero contaría más
     * pasadas de buscadores que visitas de personas. Un agente vacío también
     * se considera automatizado (ningún navegador real lo omite).
     */
    private static function esRobot(string $agente): bool
    {
        if (trim($agente) === '') {
            return true;
        }

        return (bool) preg_match(
            '/bot|crawl|spider|slurp|bingpreview|googlebot|baidu|yandex|semrush|ahrefs|lighthouse|pingdom|uptimerobot|headlesschrome|python-requests|curl|wget/i',
            $agente
        );
    }

    /**
     * Tipo de dispositivo a partir del agente de usuario. El orden importa:
     * muchas tabletas se anuncian también como "android"/"mobile".
     */
    private static function detectarDispositivo(string $agente): string
    {
        if (preg_match('/tablet|ipad|playbook|silk/i', $agente)) {
            return 'tableta';
        }

        if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|iemobile/i', $agente)) {
            return 'movil';
        }

        return 'escritorio';
    }
}
