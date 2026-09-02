<?php

namespace Tests\Unit;

use App\Models\CategoriaDocumento;
use App\Models\Documento;
use App\Models\DocumentoVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_documento_pertenece_a_una_categoria(): void
    {
        $categoria = CategoriaDocumento::factory()->create(['clave' => 'formatos']);
        $documento = Documento::factory()->create(['categoria_documento_id' => $categoria->id]);

        $this->assertTrue($documento->categoria->is($categoria));
    }

    public function test_un_documento_conserva_sus_versiones_anteriores(): void
    {
        $documento = Documento::factory()->create();
        $version = DocumentoVersion::factory()->create(['documento_id' => $documento->id]);

        $this->assertTrue($documento->versiones->contains($version));
    }

    public function test_el_scope_publicado_excluye_documentos_no_publicados(): void
    {
        Documento::factory()->create(['publicado' => false]);
        $publicado = Documento::factory()->create(['publicado' => true]);

        $resultado = Documento::publicado()->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($publicado));
    }

    public function test_el_scope_por_categoria_filtra_correctamente(): void
    {
        $categoriaUno = CategoriaDocumento::factory()->create();
        $categoriaDos = CategoriaDocumento::factory()->create();
        $documentoUno = Documento::factory()->create(['categoria_documento_id' => $categoriaUno->id]);
        Documento::factory()->create(['categoria_documento_id' => $categoriaDos->id]);

        $resultado = Documento::porCategoria($categoriaUno->id)->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($documentoUno));
    }

    public function test_el_scope_buscar_encuentra_por_nombre_o_descripcion(): void
    {
        $coincidencia = Documento::factory()->create(['nombre' => 'Formato de solicitud de auditoría']);
        Documento::factory()->create(['nombre' => 'Oficio de notificación']);

        $resultado = Documento::buscar('auditoría')->get();

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->first()->is($coincidencia));
    }
}
