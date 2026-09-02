<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enlaces institucionales de interés (RF-ENL-001/002): declaración patrimonial,
     * evaluación de control interno, entrega-recepción, ASF, SNA, etc.
     */
    public function up(): void
    {
        Schema::create('enlaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_enlace_id')->constrained('categorias_enlace')->restrictOnDelete();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->string('url', 500);
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['categoria_enlace_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enlaces');
    }
};
