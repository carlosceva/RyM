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
        Schema::table('solicitud_muestras_mercaderia', function (Blueprint $table) {
            $table->unsignedBigInteger('id_almacen')->nullable();
            $table->foreign('id_almacen')->references('id')->on('almacen')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_muestras_mercaderia', function (Blueprint $table) {
            $table->dropForeign(['id_almacen']);
            $table->dropColumn('id_almacen');
        });
    }
};
