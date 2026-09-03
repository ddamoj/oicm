<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un medio de galería puede ser un archivo subido o una URL externa
     * (YouTube/Vimeo) para video; por eso ruta_archivo pasa a ser opcional y
     * se agrega ruta_miniatura para la portada del grid público (Fase 8).
     */
    public function up(): void
    {
        Schema::table('galeria_medios', function (Blueprint $table) {
            $table->string('url_externa', 500)->nullable()->after('ruta_archivo');
            $table->string('ruta_miniatura')->nullable()->after('url_externa');
            $table->string('ruta_archivo')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('galeria_medios', function (Blueprint $table) {
            $table->dropColumn(['url_externa', 'ruta_miniatura']);
            $table->string('ruta_archivo')->nullable(false)->change();
        });
    }
};
