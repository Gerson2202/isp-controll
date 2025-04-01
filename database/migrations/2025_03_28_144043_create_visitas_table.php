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
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');  // Relación con el ticket
            $table->dateTime('fecha_inicio')->nullable();  // Fecha y hora de inicio
            $table->dateTime('fecha_cierre')->nullable();  // Fecha y hora de cierre
            $table->text('descripcion')->nullable();  // Descripción de la visita
            $table->text('solucion')->nullable();  // Descripción de la visita
            $table->string('color')->nullable();
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
