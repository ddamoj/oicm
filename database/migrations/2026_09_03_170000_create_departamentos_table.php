<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Departamentos que integran cada Dirección del OICM, para el organigrama
     * interactivo de "Quiénes somos" (Contralor + 4 direcciones + 11 departamentos).
     */
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direccion_id')->constrained('direcciones')->cascadeOnDelete();
            $table->string('nombre', 200);
            $table->string('siglas', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
