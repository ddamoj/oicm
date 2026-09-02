<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Contenido institucional editable (Quiénes somos, misión, visión, valores,
     * páginas por Dirección, estrados digitales) gestionado por el Administrador de Contenido.
     */
    public function up(): void
    {
        Schema::create('paginas_institucionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones')->nullOnDelete();
            $table->string('slug', 150)->unique();
            $table->string('titulo', 200);
            $table->longText('contenido');
            $table->string('estatus', 20)->default('borrador');
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paginas_institucionales');
    }
};
