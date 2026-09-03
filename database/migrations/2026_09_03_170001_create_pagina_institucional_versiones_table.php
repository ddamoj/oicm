<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historial de versiones de una página institucional (editor de bloques
     * versionados, Fase 7): antes de sobrescribir el contenido vigente se
     * archiva aquí el estado anterior, con su autor, para poder consultarlo
     * o restaurarlo — mismo patrón que `documento_versiones` (Fase 4).
     */
    public function up(): void
    {
        Schema::create('pagina_institucional_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagina_institucional_id')->constrained('paginas_institucionales')->cascadeOnDelete();
            $table->unsignedInteger('numero_version');
            $table->string('titulo', 200);
            $table->longText('contenido');
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pagina_institucional_id', 'numero_version'], 'pagina_inst_versiones_pagina_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagina_institucional_versiones');
    }
};
