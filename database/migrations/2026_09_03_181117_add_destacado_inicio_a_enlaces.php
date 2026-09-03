<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marca los enlaces que se muestran como "accesos rápidos" en la página
     * de inicio (Fase 8), a decisión del Administrador de Contenido en vez de
     * quedar escritos a mano en la vista.
     */
    public function up(): void
    {
        Schema::table('enlaces', function (Blueprint $table) {
            $table->boolean('destacado_inicio')->default(false)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('enlaces', function (Blueprint $table) {
            $table->dropColumn('destacado_inicio');
        });
    }
};
