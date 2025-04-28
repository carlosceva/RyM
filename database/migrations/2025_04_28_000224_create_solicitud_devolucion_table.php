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
        Schema::create('solicitud_devolucion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_solicitud')->constrained('solicitudes');

            $table->string('nota_venta');
            $table->string('cliente')->nullable();
            $table->string('almacen');
            $table->string('motivo');
            $table->string('detalle_productos')->nullable();
            $table->boolean('requiere_abono')->default(false);
            $table->boolean('tiene_entrega')->default(false);

            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_devolucion');
    }
};
