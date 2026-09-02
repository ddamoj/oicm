<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marco normativo del OICM por ámbito (federal, estatal, municipal), con
     * fecha de publicación y de última reforma, tomado del documento de carga.
     */
    public function up(): void
    {
        Schema::create('normatividad', function (Blueprint $table) {
            $table->id();
            $table->string('ambito', 20);
            $table->string('titulo', 300);
            $table->text('descripcion')->nullable();
            $table->string('medio_publicacion', 200)->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->date('fecha_ultima_reforma')->nullable();
            $table->string('documento_url')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('vigente')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ambito', 'vigente']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('normatividad');
    }
};
