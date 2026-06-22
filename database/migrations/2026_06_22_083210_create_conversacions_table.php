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
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->nullOnDelete();

            $table->string('telefono', 20);

            $table->string('nombre_contacto')->nullable();

            $table->enum('estado', [
                'abierto',
                'ia',
                'agente',
                'cerrado'
            ])->default('ia');

            $table->boolean('ia_activa')->default(true);

            $table->foreignId('asignado_a')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('ultima_actividad')->nullable();

            $table->timestamps();

            $table->index('telefono');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversacions');
    }
};
