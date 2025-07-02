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
            
            $table->unsignedBigInteger('id_confirmador')->nullable();
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->foreign('id_confirmador')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_sobregiro', function (Blueprint $table) {
            // Para revertir los cambios si hacés rollback
            $table->dropForeign(['id_confirmador']);
            $table->dropColumn(['id_confirmador', 'fecha_confirmacion']);
        });
    }
};
