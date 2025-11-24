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
        Schema::create('solicitud_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_solicitud')->constrained('solicitudes');
            $table->string('tipo_extra');
            $table->date('fecha_solicitud')->nullable();
            $table->string('estado')->default('pendiente');
            $table->unsignedBigInteger('id_confirmador')->nullable();
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->timestamps();
            
            $table->foreign('id_confirmador')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_extras');
    }
};
