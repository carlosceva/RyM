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
        Schema::table('solicitud_precio_especial', function (Blueprint $table) {
            $table->string('venta_realizada', 1)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_precio_especial', function (Blueprint $table) {
             $table->dropColumn('venta_realizada');
        });
    }
};
