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
        Schema::table('almacen', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id_encargado')->nullable();
            $table->foreign('id_encargado')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('almacen', function (Blueprint $table) {
            // Para revertir los cambios si hacés rollback
            $table->dropForeign(['id_encargado']);
            $table->dropColumn(['id_encargado']);
        });
    }
};
