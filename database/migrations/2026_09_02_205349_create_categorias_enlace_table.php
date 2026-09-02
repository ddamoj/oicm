<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo de categorías de enlaces externos (RF-ENL-001/002): Transparencia,
     * Normativa, Declaraciones, Evaluación de control interno, Entrega-recepción, etc.
     */
    public function up(): void
    {
        Schema::create('categorias_enlace', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 40)->unique();
            $table->string('nombre', 100);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_enlace');
    }
};
