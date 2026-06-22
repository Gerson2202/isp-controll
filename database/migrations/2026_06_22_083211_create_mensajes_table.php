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
         Schema::create('mensajes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversacion_id')
                ->constrained('conversaciones')
                ->cascadeOnDelete();

            $table->enum('tipo', [
                'cliente',
                'agente',
                'ia',
                'sistema'
            ]);

            $table->enum('tipo_contenido', [
                'texto',
                'imagen',
                'audio',
                'video',
                'documento'
            ])->default('texto');

            $table->longText('mensaje')->nullable();

            $table->string('archivo_url')->nullable();

            $table->string('whatsapp_message_id')->nullable();

            $table->enum('estado_whatsapp', [
                'enviado',
                'entregado',
                'leido',
                'error'
            ])->nullable();

            $table->timestamp('fecha_mensaje');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
