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
        Schema::create('solicitud_anulacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_solicitud')->constrained('solicitudes');
            $table->string('nota_venta');
            $table->string('motivo');
            $table->boolean('tiene_pago')->nullable();
            $table->boolean('tiene_entrega')->nullable();
            $table->boolean('entrega_fisica')->nullable();
            $table->string('obs_pago')->nullable();

            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_anulacion');
    }
};
