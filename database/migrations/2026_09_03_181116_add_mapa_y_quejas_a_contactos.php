<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coordenadas para el mapa embebido, canal de quejas y denuncias de la
     * DQDISP y orden explícito del directorio de contacto (Fase 8).
     */
    public function up(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->decimal('latitud', 10, 7)->nullable()->after('horario');
            $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            $table->string('canal_quejas_denuncias', 500)->nullable()->after('longitud');
            $table->unsignedInteger('orden')->default(0)->after('canal_quejas_denuncias');
        });
    }

    public function down(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud', 'canal_quejas_denuncias', 'orden']);
        });
    }
};
