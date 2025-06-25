<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitud_sobregiro', function (Blueprint $table) {
            // Agregar columna cod_sobregiro como string nullable
            $table->string('cod_sobregiro')->nullable();

            // Cambiar la precisión de importe a 2 decimales
            // Para modificar columnas necesitas la extensión doctrine/dbal instalada
            $table->decimal('importe', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_sobregiro', function (Blueprint $table) {
            $table->dropColumn('cod_sobregiro');

            // Revertir importe a 4 decimales (como estaba antes)
            $table->decimal('importe', 10, 4)->change();
        });
    }
};
