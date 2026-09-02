<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Documentos públicos (RF-CAR-001/002/003, RF-DES-001/002): formatos, oficios
     * y bases de datos. La ruta física apunta siempre a la versión vigente;
     * las versiones anteriores se conservan en documento_versiones.
     */
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_documento_id')->constrained('categorias_documento')->restrictOnDelete();
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones')->nullOnDelete();
            $table->string('nombre', 250);
            $table->text('descripcion')->nullable();
            $table->string('ruta_archivo');
            $table->string('nombre_original', 250);
            $table->string('extension', 10);
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('tamano_bytes');
            $table->unsignedInteger('contador_descargas')->default(0);
            $table->boolean('publicado')->default(true);
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['categoria_documento_id', 'publicado']);
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
