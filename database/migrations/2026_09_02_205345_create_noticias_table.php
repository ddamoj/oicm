<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Noticias y comunicados (RF-NOT-001/002/003): título, cuerpo, imagen,
     * fecha de publicación y estatus Borrador/Publicada.
     */
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 250);
            $table->string('slug', 250)->unique();
            $table->longText('contenido');
            $table->string('imagen_portada')->nullable();
            $table->string('estatus', 20)->default('borrador');
            $table->timestamp('publicado_en')->nullable();
            $table->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['estatus', 'publicado_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
