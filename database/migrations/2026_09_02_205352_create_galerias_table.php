<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Galerías de eventos (capacitaciones, sesiones COCODI, Comité de Obras,
     * entrega-recepción); cada galería agrupa varios medios (galeria_medios).
     */
    public function up(): void
    {
        Schema::create('galerias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->date('fecha_evento')->nullable();
            $table->boolean('publicada')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galerias');
    }
};
