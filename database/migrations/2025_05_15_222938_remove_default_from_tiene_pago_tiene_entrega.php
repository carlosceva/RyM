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
        Schema::table('solicitud_devolucion', function (Blueprint $table) { 
            // Eliminar el valor por defecto de las columnas
            $table->boolean('tiene_pago')->nullable()->change(); 
            $table->boolean('tiene_entrega')->nullable()->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_devolucion', function (Blueprint $table) {
            //
        });
    }
};
