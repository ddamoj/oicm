<?php

namespace App\Livewire\Admin\Estadisticas;

use App\Models\VisitaPagina;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Tablero de estadísticas de visitas al micrositio público.
 *
 * Analítica propia, sin servicios de terceros ni cookies de rastreo: se
 * alimenta de [VisitaPagina], que solo guarda ruta, dispositivo y una huella
 * irreversible de la IP.
 */
class TableroVisitas extends Component
{
    /** Periodos ofrecidos, en días. */
    public const PERIODOS = [
        '7' => 'Últimos 7 días',
        '30' => 'Últimos 30 días',
        '90' => 'Últimos 90 días',
    ];

    #[Url(as: 'periodo')]
    public string $periodo = '30';

    /**
     * Días del periodo elegido, saneado: la propiedad viaja en la URL, así
     * que nunca se usa el valor crudo en una consulta.
     */
    private function dias(): int
    {
        return array_key_exists($this->periodo, self::PERIODOS) ? (int) $this->periodo : 30;
    }

    /**
     * Cifras de cabecera. Las visitas "únicas" se cuentan por huella de IP
     * distinta, que es una aproximación: varias personas tras una misma
     * salida a internet comparten huella.
     *
     * @return array{hoy: int, unicosHoy: int, semana: int, mes: int, unicosMes: int, total: int, unicosTotal: int}
     */
    #[Computed]
    public function indicadores(): array
    {
        try {
            return [
                'hoy' => VisitaPagina::query()->hoy()->count(),
                'unicosHoy' => VisitaPagina::query()->hoy()->distinct()->count('ip_huella'),
                'semana' => VisitaPagina::query()->estaSemana()->count(),
                'mes' => VisitaPagina::query()->esteMes()->count(),
                'unicosMes' => VisitaPagina::query()->esteMes()->distinct()->count('ip_huella'),
                'total' => VisitaPagina::query()->count(),
                'unicosTotal' => VisitaPagina::query()->distinct()->count('ip_huella'),
            ];
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular los indicadores de visitas.', ['error' => $excepcion->getMessage()]);

            return ['hoy' => 0, 'unicosHoy' => 0, 'semana' => 0, 'mes' => 0, 'unicosMes' => 0, 'total' => 0, 'unicosTotal' => 0];
        }
    }

    /**
     * Serie diaria del periodo. Se rellenan los días sin visitas con cero
     * para que la gráfica no comprima el eje y se lea la caída real.
     *
     * @return list<array{etiqueta: string, fecha: string, total: int}>
     */
    #[Computed]
    public function porDia(): array
    {
        $dias = $this->dias();

        try {
            $registros = VisitaPagina::query()
                ->ultimosDias($dias)
                ->selectRaw('DATE(visitada_en) as fecha, COUNT(*) as total')
                ->groupBy('fecha')
                ->pluck('total', 'fecha')
                ->all();

            $serie = [];

            for ($i = $dias - 1; $i >= 0; $i--) {
                $dia = now()->subDays($i);
                $clave = $dia->format('Y-m-d');

                $serie[] = [
                    'etiqueta' => $dia->format('d/m'),
                    'fecha' => $dia->translatedFormat('j \d\e F'),
                    'total' => (int) ($registros[$clave] ?? 0),
                ];
            }

            return $serie;
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular la serie diaria de visitas.', ['error' => $excepcion->getMessage()]);

            return [];
        }
    }

    /**
     * Páginas más visitadas del periodo, con su número de visitantes
     * distintos.
     *
     * @return list<array{ruta: string, total: int, unicos: int}>
     */
    #[Computed]
    public function topPaginas(): array
    {
        try {
            return VisitaPagina::query()
                ->ultimosDias($this->dias())
                ->selectRaw('ruta, COUNT(*) as total, COUNT(DISTINCT ip_huella) as unicos')
                ->groupBy('ruta')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($fila): array => [
                    'ruta' => (string) $fila->ruta,
                    'total' => (int) $fila->total,
                    'unicos' => (int) $fila->unicos,
                ])
                ->all();
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular las páginas más visitadas.', ['error' => $excepcion->getMessage()]);

            return [];
        }
    }

    /**
     * Reparto por tipo de dispositivo, en el orden fijo escritorio / móvil /
     * tableta para que las barras no bailen entre recargas.
     *
     * @return list<array{tipo: string, etiqueta: string, total: int, porcentaje: float}>
     */
    #[Computed]
    public function porDispositivo(): array
    {
        $etiquetas = ['escritorio' => 'Escritorio', 'movil' => 'Móvil', 'tableta' => 'Tableta'];

        try {
            $registros = VisitaPagina::query()
                ->ultimosDias($this->dias())
                ->selectRaw('dispositivo, COUNT(*) as total')
                ->groupBy('dispositivo')
                ->pluck('total', 'dispositivo')
                ->all();

            // Divisor mínimo 1: evita la división entre cero cuando el
            // periodo todavía no tiene ninguna visita.
            $suma = max(array_sum($registros), 1);

            return array_map(fn (string $tipo): array => [
                'tipo' => $tipo,
                'etiqueta' => $etiquetas[$tipo],
                'total' => (int) ($registros[$tipo] ?? 0),
                'porcentaje' => round(((int) ($registros[$tipo] ?? 0)) / $suma * 100, 1),
            ], array_keys($etiquetas));
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular el reparto por dispositivo.', ['error' => $excepcion->getMessage()]);

            return [];
        }
    }

    /**
     * Tendencia de los últimos 12 meses, independiente del periodo elegido.
     *
     * Se agrupa con `DATE_FORMAT` (MySQL, el motor del proyecto); el módulo
     * original usaba `EXTRACT(... FROM ...)`, que es sintaxis de PostgreSQL.
     *
     * @return list<array{mes: string, total: int}>
     */
    #[Computed]
    public function porMes(): array
    {
        try {
            $registros = VisitaPagina::query()
                ->where('visitada_en', '>=', now()->subMonths(11)->startOfMonth())
                ->selectRaw("DATE_FORMAT(visitada_en, '%Y-%m') as periodo, COUNT(*) as total")
                ->groupBy('periodo')
                ->pluck('total', 'periodo')
                ->all();

            $serie = [];

            for ($i = 11; $i >= 0; $i--) {
                $mes = now()->subMonths($i);

                $serie[] = [
                    'mes' => ucfirst($mes->translatedFormat('M Y')),
                    'total' => (int) ($registros[$mes->format('Y-m')] ?? 0),
                ];
            }

            return $serie;
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular la tendencia mensual de visitas.', ['error' => $excepcion->getMessage()]);

            return [];
        }
    }

    /**
     * Mayor total de la serie diaria, para escalar las barras. Nunca menor
     * que 1, de nuevo para no dividir entre cero.
     */
    #[Computed]
    public function maximoDiario(): int
    {
        return max(array_column($this->porDia(), 'total') ?: [0]) ?: 1;
    }

    /**
     * Mayor total de la serie mensual, para escalar la tendencia.
     */
    #[Computed]
    public function maximoMensual(): int
    {
        return max(array_column($this->porMes(), 'total') ?: [0]) ?: 1;
    }

    public function render()
    {
        $this->authorize('viewAny', VisitaPagina::class);

        return view('livewire.admin.estadisticas.tablero-visitas');
    }
}
