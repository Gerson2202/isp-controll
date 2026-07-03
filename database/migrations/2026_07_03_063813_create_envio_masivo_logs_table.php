<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('envio_masivo_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->enum('estado', ['pendiente', 'enviado', 'error', 'programado']);
            $table->string('plantilla_usada')->nullable();
            $table->timestamp('fecha_envio')->nullable();
            $table->json('response_data')->nullable();
            $table->string('ip_cliente')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('factura_id')->references('id')->on('facturas')->onDelete('set null');

            $table->index(['estado', 'fecha_envio']);
            $table->index('cliente_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('envio_masivo_logs');
    }
};
