<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fotos y videos individuales que pertenecen a una galería.
     */
    public function up(): void
    {
        Schema::create('galeria_medios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('galeria_id')->constrained('galerias')->cascadeOnDelete();
            $table->string('tipo', 10);
            $table->string('ruta_archivo');
            $table->string('descripcion_alt', 250)->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeria_medios');
    }
};
