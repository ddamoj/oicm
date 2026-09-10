<?php

namespace App\Livewire\Admin\Panel;

use App\Models\BitacoraAuditoria;
use App\Models\Documento;
use App\Models\Enlace;
use App\Models\Estrado;
use App\Models\Normatividad;
use App\Models\Noticia;
use App\Models\PaginaInstitucional;
use App\Models\User;
use App\Models\VisitaPagina;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Panel principal de la administración: el estado del micrositio de un
 * vistazo. Responde tres preguntas al entrar — cuánto contenido está
 * publicado, qué quedó a medias y qué se hizo últimamente — en vez de servir
 * de portada vacía.
 *
 * Todo lo que muestra respeta el rol: el bloque de actividad reciente solo se
 * calcula para quien puede consultar la bitácora (rol "administrador").
 */
class ResumenPanel extends Component
{
    /**
     * Conteos de contenido publicado, con su ruta de gestión. Se resuelven en
     * una sola consulta agregada por modelo en vez de una por cifra.
     *
     * @return list<array{etiqueta: string, total: int, ruta: string, detalle: string}>
     */
    #[Computed]
    public function publicado(): array
    {
        try {
            $documentos = Documento::query()->selectRaw(
                'sum(case when publicado = 1 then 1 else 0 end) as publicados,
                 sum(case when publicado = 0 then 1 else 0 end) as pendientes'
            )->first();

            $noticias = Noticia::query()->selectRaw(
                "sum(case when estatus = 'publicada' and publicado_en is not null and publicado_en <= ? then 1 else 0 end) as publicadas",
                [now()]
            )->first();

            return [
                [
                    'etiqueta' => 'Documentos',
                    'total' => (int) ($documentos->publicados ?? 0),
                    'ruta' => 'admin.documentos',
                    'detalle' => ((int) ($documentos->pendientes ?? 0)).' sin publicar',
                ],
                [
                    'etiqueta' => 'Noticias',
                    'total' => (int) ($noticias->publicadas ?? 0),
                    'ruta' => 'admin.noticias',
                    'detalle' => 'visibles en el portal',
                ],
                [
                    'etiqueta' => 'Enlaces',
                    'total' => Enlace::query()->where('activo', true)->count(),
                    'ruta' => 'admin.enlaces',
                    'detalle' => 'en el directorio',
                ],
                [
                    'etiqueta' => 'Normatividad',
                    'total' => Normatividad::query()->where('vigente', true)->count(),
                    'ruta' => 'admin.normatividad',
                    'detalle' => 'disposiciones vigentes',
                ],
            ];
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular el resumen de contenido publicado.', ['error' => $excepcion->getMessage()]);

            return [];
        }
    }

    /**
     * Trabajo a medias que nadie ve en el portal todavía: borradores, material
     * sin publicar y noticias programadas a futuro. Es la parte accionable del
     * panel, así que cada renglón enlaza al módulo donde se resuelve.
     *
     * @return list<array{texto: string, ruta: string, variante: string}>
     */
    #[Computed]
    public function pendientes(): array
    {
        try {
            $pendientes = [];

            $noticias = Noticia::query()->selectRaw(
                "sum(case when estatus = 'borrador' then 1 else 0 end) as borradores,
                 sum(case when estatus = 'publicada' and publicado_en > ? then 1 else 0 end) as programadas",
                [now()]
            )->first();

            $borradoresNoticia = (int) ($noticias->borradores ?? 0);
            $programadas = (int) ($noticias->programadas ?? 0);
            $borradoresPagina = PaginaInstitucional::query()->where('estatus', 'borrador')->count();
            $documentosSinPublicar = Documento::query()->where('publicado', false)->count();
            $estradosRetirados = Estrado::query()->where('activo', false)->count();

            if ($borradoresNoticia > 0) {
                $pendientes[] = [
                    'texto' => $this->frase($borradoresNoticia, 'noticia en borrador', 'noticias en borrador'),
                    'ruta' => 'admin.noticias',
                    'variante' => 'advertencia',
                ];
            }

            if ($programadas > 0) {
                $pendientes[] = [
                    'texto' => $this->frase($programadas, 'noticia programada', 'noticias programadas').' a futuro',
                    'ruta' => 'admin.noticias',
                    'variante' => 'primario',
                ];
            }

            if ($borradoresPagina > 0) {
                $pendientes[] = [
                    'texto' => $this->frase($borradoresPagina, 'página institucional en borrador', 'páginas institucionales en borrador'),
                    'ruta' => 'admin.paginas',
                    'variante' => 'advertencia',
                ];
            }

            if ($documentosSinPublicar > 0) {
                $pendientes[] = [
                    'texto' => $this->frase($documentosSinPublicar, 'documento sin publicar', 'documentos sin publicar'),
                    'ruta' => 'admin.documentos',
                    'variante' => 'advertencia',
                ];
            }

            if ($estradosRetirados > 0) {
                $pendientes[] = [
                    'texto' => $this->frase($estradosRetirados, 'estrado retirado', 'estrados retirados'),
                    'ruta' => 'admin.estrados',
                    'variante' => 'neutro',
                ];
            }

            return $pendientes;
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular los pendientes del panel.', ['error' => $excepcion->getMessage()]);

            return [];
        }
    }

    /**
     * Últimos movimientos registrados en la bitácora. Solo para el rol
     * "administrador": es el mismo criterio que protege `/admin/bitacora`.
     *
     * @return Collection<int, BitacoraAuditoria>
     */
    #[Computed]
    public function actividad(): Collection
    {
        if (! Gate::allows('gestionar-usuarios')) {
            return new Collection;
        }

        try {
            return BitacoraAuditoria::query()
                ->with('usuario:id,name')
                ->latest('creado_en')
                ->limit(8)
                ->get();
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible cargar la actividad reciente del panel.', ['error' => $excepcion->getMessage()]);

            return new Collection;
        }
    }

    /**
     * Cuentas con acceso al panel, para que el administrador detecte de un
     * vistazo altas o bajas inesperadas.
     *
     * @return array{activas: int, inactivas: int}|null
     */
    #[Computed]
    public function cuentas(): ?array
    {
        if (! Gate::allows('gestionar-usuarios')) {
            return null;
        }

        try {
            $conteo = User::query()->selectRaw(
                'sum(case when activo = 1 then 1 else 0 end) as activas,
                 sum(case when activo = 0 then 1 else 0 end) as inactivas'
            )->first();

            return [
                'activas' => (int) ($conteo->activas ?? 0),
                'inactivas' => (int) ($conteo->inactivas ?? 0),
            ];
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular el resumen de cuentas.', ['error' => $excepcion->getMessage()]);

            return null;
        }
    }

    /**
     * Resumen de visitas al micrositio público, para no tener que entrar al
     * tablero completo solo para ver el pulso del día.
     *
     * @return array{hoy: int, mes: int}
     */
    #[Computed]
    public function visitas(): array
    {
        try {
            return [
                'hoy' => VisitaPagina::query()->hoy()->count(),
                'mes' => VisitaPagina::query()->esteMes()->count(),
            ];
        } catch (\Throwable $excepcion) {
            Log::error('No fue posible calcular el resumen de visitas del panel.', ['error' => $excepcion->getMessage()]);

            return ['hoy' => 0, 'mes' => 0];
        }
    }

    /**
     * Concuerda el número con su sustantivo: "1 noticia en borrador" contra
     * "3 noticias en borrador". `Str::plural()` aplica reglas del inglés y no
     * sirve para estas frases.
     */
    private function frase(int $total, string $singular, string $plural): string
    {
        return $total.' '.($total === 1 ? $singular : $plural);
    }

    /**
     * Convierte la acción almacenada (`pagina_institucional_actualizada`) en
     * una frase legible ("Página institucional actualizada").
     *
     * Las acciones se guardan sin acentos porque son identificadores; para
     * mostrarlas se restituyen con un diccionario mínimo de las palabras del
     * dominio que sí los llevan. Es preferible a un mapa acción por acción,
     * que habría que ampliar con cada módulo nuevo.
     */
    public function etiquetaAccion(string $accion): string
    {
        $conAcento = [
            'pagina' => 'página',
            'bitacora' => 'bitácora',
            'direccion' => 'dirección',
            'galeria' => 'galería',
            'normatividad' => 'normatividad',
        ];

        $palabras = array_map(
            fn (string $palabra): string => $conAcento[$palabra] ?? $palabra,
            explode('_', $accion)
        );

        return ucfirst(implode(' ', $palabras));
    }

    /**
     * Autor de una entrada de bitácora. Distingue los dos casos en que no hay
     * nombre: una cuenta borrada (quedó el `user_id`) y una acción sin sesión
     * iniciada, como un intento de acceso fallido.
     */
    public function autorDe(BitacoraAuditoria $registro): string
    {
        if ($registro->usuario?->name) {
            return $registro->usuario->name;
        }

        return $registro->user_id !== null ? 'Cuenta eliminada' : 'Sin sesión iniciada';
    }

    public function render()
    {
        $this->authorize('viewAny', Documento::class);

        return view('livewire.admin.panel.resumen-panel');
    }
}
