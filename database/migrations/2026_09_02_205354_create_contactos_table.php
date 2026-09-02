<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Directorio de contacto por dirección: domicilio, teléfono, correo y horario.
     */
    public function up(): void
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones')->nullOnDelete();
            $table->string('nombre_area', 200);
            $table->string('domicilio', 300)->nullable();
            $table->string('telefono', 40)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('horario', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
