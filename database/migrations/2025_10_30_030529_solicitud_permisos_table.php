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
        Schema::create('solicitud_vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_solicitud')->constrained('solicitudes');
            $table->date('fecha_inicial');
            $table->date('fecha_fin');
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_vacaciones');
    }
};
