<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estrados digitales de la DRACS (RF-INS, riesgo 9 del plan de trabajo):
     * listado numerado y consultable de notificaciones, con constancia
     * probatoria de publicación (fecha/hora y hash del archivo).
     */
    public function up(): void
    {
        Schema::create('estrados', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('numero')->unique();
            $table->string('expediente', 100)->nullable();
            $table->string('asunto', 300);
            $table->dateTime('fecha_publicacion');
            $table->string('archivo_ruta');
            $table->string('archivo_nombre', 250);
            $table->string('archivo_extension', 10);
            $table->string('archivo_mime', 150);
            $table->unsignedBigInteger('archivo_tamano');
            // Huella SHA-256 del archivo publicado: constancia de integridad
            // para el valor probatorio de la notificación por estrado.
            $table->string('archivo_hash', 64);
            // Confirmación obligatoria del Administrador de Contenido de que el
            // documento ya pasó por el procedimiento de testado de datos
            // personales antes de su publicación (riesgo alto del plan de trabajo).
            $table->boolean('datos_testados')->default(false);
            $table->foreignId('publicado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['activo', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estrados');
    }
};
