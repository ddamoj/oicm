<?php

namespace App\Livewire\Publico;

use App\Models\Documento;
use App\Models\Enlace;
use App\Models\Normatividad;
use App\Models\Noticia;
use App\Models\PaginaInstitucional;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Buscador global del micrositio (Fase 8): consulta Documentos, Noticias,
 * Enlaces, Normatividad y Páginas institucionales por separado, aplicando
 * siempre el scopePublicado de cada modelo para nunca filtrar contenido no
 * publicado. Sin tabla índice: el volumen de cada tabla es bajo y así se
 * evita el riesgo de desincronización entre el índice y el contenido real.
 */
class BuscadorGlobal extends Component
{
    #[Url(as: 'q')]
    public string $termino = '';

    private const LIMITE_POR_SECCION = 8;

    /**
     * Cada bloque limita resultados y respeta la visibilidad pública de su
     * propio modelo; null-safety: un término corto o vacío no ejecuta ninguna consulta.
     *
     * @return array<string, Collection>
     */
    #[Computed]
    public function resultados(): array
    {
        $termino = trim($this->termino);

        if (mb_strlen($termino) < 3) {
            return [];
        }

        try {
            return [
                'documentos' => Documento::query()->publicado()->buscar($termino)->limit(self::LIMITE_POR_SECCION)->get(),
                'noticias' => Noticia::query()->publicado()->buscar($termino)->limit(self::LIMITE_POR_SECCION)->get(),
                'enlaces' => Enlace::query()->publicado()->buscar($termino)->limit(self::LIMITE_POR_SECCION)->get(),
                'normatividad' => Normatividad::query()->publicado()->buscar($termino)->limit(self::LIMITE_POR_SECCION)->get(),
                'paginas' => PaginaInstitucional::query()->publicado()->with('direccion')->buscar($termino)->limit(self::LIMITE_POR_SECCION)->get(),
            ];
        } catch (\Throwable) {
            return [];
        }
    }

    public function totalResultados(): int
    {
        return collect($this->resultados())->sum(fn ($coleccion) => $coleccion->count());
    }

    /**
     * Configuración de ruta y título por sección, resuelta aquí (no en la
     * vista) para que el Blade del componente mantenga un único elemento
     * raíz, como exige Livewire.
     *
     * @return array<string, array{etiqueta: string, ruta: \Closure, titulo: \Closure}>
     */
    private function secciones(): array
    {
        return [
            'documentos' => ['etiqueta' => 'Documentos', 'ruta' => fn ($item) => route('documentos').'?q='.urlencode($item->nombre), 'titulo' => fn ($item) => $item->nombre],
            'noticias' => ['etiqueta' => 'Noticias', 'ruta' => fn ($item) => route('noticias.mostrar', $item), 'titulo' => fn ($item) => $item->titulo],
            'enlaces' => ['etiqueta' => 'Enlaces de interés', 'ruta' => fn ($item) => $item->url, 'titulo' => fn ($item) => $item->nombre],
            'normatividad' => ['etiqueta' => 'Normatividad', 'ruta' => fn ($item) => route('normatividad').'?ambito='.$item->ambito, 'titulo' => fn ($item) => $item->titulo],
            'paginas' => ['etiqueta' => 'Información institucional', 'ruta' => fn ($item) => $item->urlPublica(), 'titulo' => fn ($item) => $item->titulo],
        ];
    }

    public function render()
    {
        $termino = trim($this->termino);

        return view('livewire.publico.buscador-global', [
            'secciones' => $this->secciones(),
            'descripcionBuscador' => $termino !== '' ? "Resultados para «{$termino}»" : 'Escribe un término para buscar en todo el micrositio.',
        ]);
    }
}
