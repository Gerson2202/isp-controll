<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio', 10, 2)->nullable();

            // Velocidades básicas (ya existentes)
            $table->integer('velocidad_bajada'); // en Mbps     
            $table->integer('velocidad_subida'); // en Mbps

            // Campos para rafagas (burst)
            $table->integer('rafaga_max_bajada')->nullable();
            $table->integer('rafaga_max_subida')->nullable();

            // Campos para velocidades medias (average rate)
            $table->integer('velocidad_media_bajada')->nullable();
            $table->integer('velocidad_media_subida')->nullable();

            // Tiempos de ráfaga (burst time)
            $table->integer('tiempo_rafaga_bajada')->nullable();
            $table->integer('tiempo_rafaga_subida')->nullable();

            // Otros campos útiles para queues de MikroTik
            $table->integer('prioridad')->nullable();

            $table->string('rehuso', 10);
            $table->foreignId('nodo_id')
                ->nullable()
                ->constrained('nodos')
                ->onDelete('set null');

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
