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
        Schema::table('solicitud_extras', function (Blueprint $table) {
            $table->unsignedBigInteger('id_confirmador')->nullable()->after('estado');
            $table->timestamp('fecha_confirmacion')->nullable()->after('id_confirmador');

            // FK
            $table->foreign('id_confirmador')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();   // si el usuario es eliminado, deja NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_extras', function (Blueprint $table) {
            $table->dropForeign(['id_confirmador']);
            $table->dropColumn(['id_confirmador', 'fecha_confirmacion']);
        });
    }
};
