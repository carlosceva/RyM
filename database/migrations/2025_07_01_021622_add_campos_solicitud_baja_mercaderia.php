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
            
            $table->unsignedBigInteger('id_autorizador')->nullable();
            $table->timestamp('fecha_autorizacion')->nullable();
            $table->string('tipo')->nullable();
            $table->foreign('id_autorizador')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_bajas_mercaderia', function (Blueprint $table) {
            // Para revertir los cambios si hacés rollback
            $table->dropForeign(['id_autorizador']);
            $table->dropColumn(['id_autorizador', 'fecha_autorizacion', 'tipo']);
        });
    }
};
