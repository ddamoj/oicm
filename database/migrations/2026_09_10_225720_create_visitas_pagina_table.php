<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Visitas a las páginas públicas del micrositio, para el tablero de
     * estadísticas del panel administrativo.
     *
     * No se guarda ningún dato personal: la IP solo se conserva como huella
     * HMAC-SHA256 (con `APP_KEY` como llave) para poder contar visitantes
     * distintos sin poder reconstruir la dirección de origen.
     */
    public function up(): void
    {
        Schema::create('visitas_pagina', function (Blueprint $table) {
            $table->id();
            $table->string('ruta', 255);
            $table->char('ip_huella', 64);
            $table->string('dispositivo', 20)->default('escritorio');
            $table->timestamp('visitada_en')->useCurrent();

            // Índices pensados para las consultas del tablero: la serie por
            // día y el "top" de páginas siempre acotan por fecha.
            $table->index('visitada_en');
            $table->index(['ruta', 'visitada_en']);
            $table->index(['ip_huella', 'visitada_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas_pagina');
    }
};
