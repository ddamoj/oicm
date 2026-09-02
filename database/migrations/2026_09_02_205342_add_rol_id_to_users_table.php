<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Asocia cada usuario administrativo a un rol (RF-USR-001/002) y agrega
     * softDeletes para poder revocar acceso de forma inmediata sin perder historial.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rol_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->boolean('activo')->default(true)->after('rol_id');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rol_id');
            $table->dropColumn('activo');
            $table->dropSoftDeletes();
        });
    }
};
