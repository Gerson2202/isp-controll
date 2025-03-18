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
        Schema::create('plans', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->text('descripcion');
        $table->integer('velocidad_bajada');
        $table->integer('velocidad_subida');
        $table->string('rehuso', 10);
        $table->foreignId('nodo_id')
        ->nullable() // Esta línea permite que el campo sea nulo
        ->constrained('nodos') // Definimos la relación con la tabla 'nodos'
        ->onDelete('set null'); // Si se elimina un nodo, el campo 'nodo_id' se estab
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
