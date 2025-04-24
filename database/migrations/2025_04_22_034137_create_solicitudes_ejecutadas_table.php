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
        Schema::create('solicitudes_ejecutadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solicitud_id')->unique();
            $table->unsignedBigInteger('ejecutado_por');
            $table->timestamp('fecha_ejecucion')->useCurrent();
            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->onDelete('cascade');
            $table->foreign('ejecutado_por')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_ejecutadas');
    }
};
