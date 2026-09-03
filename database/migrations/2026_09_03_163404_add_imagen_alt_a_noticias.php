<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Completa el modelo de Noticia (Fase 5, RF-NOT-001): texto alternativo
     * obligatorio de la imagen (accesibilidad), ruta de la miniatura generada
     * y un resumen corto para tarjetas de listado y meta description.
     */
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->string('resumen', 300)->nullable()->after('contenido');
            $table->string('imagen_alt')->nullable()->after('imagen_portada');
            $table->string('imagen_miniatura')->nullable()->after('imagen_alt');
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropColumn(['resumen', 'imagen_alt', 'imagen_miniatura']);
        });
    }
};
