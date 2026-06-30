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
         Schema::create('saldos_acumulados', function (Blueprint $table) {
            $table->id();
            
            // Año y mes del saldo
            $table->year('ano');
            $table->unsignedTinyInteger('mes'); // 1-12
            
            // Saldo acumulado del mes
            $table->decimal('saldo_acumulado', 12, 2)->default(0);
            
            $table->timestamps();
            
            // Índices y restricciones
            $table->unique(['ano', 'mes'], 'unique_ano_mes');
            $table->index(['ano', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldos_acumulados');
    }
};
