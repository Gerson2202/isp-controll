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
        Schema::create('consumible_movimientos', function (Blueprint $table) {
           $table->id();
            $table->foreignId('consumible_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad');
            $table->enum('tipo_movimiento',['entrada','salida','traslado']);
            $table->string('origen_tipo')->nullable();   // bodega, cliente, nodo, usuario, visita
            $table->unsignedBigInteger('origen_id')->nullable();
            $table->string('destino_tipo')->nullable();
            $table->unsignedBigInteger('destino_id')->nullable();
            $table->text('descripcion')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumible_movimientos');
    }
};
