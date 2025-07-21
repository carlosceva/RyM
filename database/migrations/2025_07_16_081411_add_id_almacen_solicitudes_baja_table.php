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
        Schema::table('solicitud_bajas_mercaderia', function (Blueprint $table) {
            // Agregar el nuevo campo id_almacen
            $table->unsignedBigInteger('id_almacen')->nullable();

            // Establecer la relación con la tabla almacenes
            $table->foreign('id_almacen')->references('id')->on('almacen')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_bajas_mercaderia', function (Blueprint $table) {
            // Eliminar la relación y columna si hacemos rollback
            $table->dropForeign(['id_almacen']);
            $table->dropColumn('id_almacen');
        });
    }
};
