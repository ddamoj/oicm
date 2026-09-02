<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bitácora de auditoría de acciones sensibles (quién, qué, cuándo, IP),
     * requerida por la Fase 3 para trazabilidad de administración.
     */
    public function up(): void
    {
        Schema::create('bitacora_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 100);
            $table->string('modelo_afectado', 100)->nullable();
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->json('detalle')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 300)->nullable();
            $table->timestamp('creado_en')->useCurrent();

            $table->index(['modelo_afectado', 'modelo_id']);
            $table->index('accion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_auditoria');
    }
};
