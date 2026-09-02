<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historial de versiones de un documento (RF-CAR-002): al reemplazar el
     * archivo vigente, la versión anterior se conserva aquí para consulta.
     */
    public function up(): void
    {
        Schema::create('documento_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete();
            $table->unsignedInteger('numero_version');
            $table->string('ruta_archivo');
            $table->string('nombre_original', 250);
            $table->string('extension', 10);
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('tamano_bytes');
            $table->foreignId('reemplazado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['documento_id', 'numero_version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_versiones');
    }
};
