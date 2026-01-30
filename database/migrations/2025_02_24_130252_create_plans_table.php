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

            // Velocidades básicas (ya existentes)
            $table->integer('velocidad_bajada'); // en Mbps
            $table->integer('velocidad_subida'); // en Mbps

            // Campos para rafagas (burst)
            $table->integer('rafaga_max_bajada')->nullable()->comment('Velocidad máxima de ráfaga bajada en kbps');
            $table->integer('rafaga_max_subida')->nullable()->comment('Velocidad máxima de ráfaga subida en kbps');

            // Campos para velocidades medias (average rate)
            $table->integer('velocidad_media_bajada')->nullable()->comment('Velocidad media bajada en kbps');
            $table->integer('velocidad_media_subida')->nullable()->comment('Velocidad media subida en kbps');

            // Tiempos de ráfaga (burst time)
            $table->integer('tiempo_rafaga_bajada')->nullable()->comment('Tiempo de ráfaga bajada en segundos');
            $table->integer('tiempo_rafaga_subida')->nullable()->comment('Tiempo de ráfaga subida en segundos');

            // Otros campos útiles para queues de MikroTik
            $table->integer('prioridad')->nullable()->comment('Prioridad de la cola (1-8)');
            $table->integer('limit_at')->nullable()->comment('Limit at (velocidad garantizada) en kbps');
            $table->integer('max_limit')->nullable()->comment('Max limit (límite máximo) en kbps');

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
