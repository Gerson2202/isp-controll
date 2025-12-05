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
        Schema::create('visitas', function (Blueprint $table) {

            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('cascade');
            $table->text('titulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('solucion')->nullable();
            $table->text('observacion')->nullable();
            $table->enum('estado', ['Pendiente', 'En progreso', 'Completada'])->default('Pendiente');  // Estado de la visita
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
