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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventarios')->onDelete('cascade');
            $table->string('tipo_movimiento'); // entrada, salida, traslado, asignacion
            $table->text('descripcion');
            
            // Ubicación anterior
            $table->foreignId('bodega_anterior_id')->nullable()->constrained('bodegas');
            $table->foreignId('user_anterior_id')->nullable()->constrained('users');
            $table->foreignId('nodo_anterior_id')->nullable()->constrained('nodos');
            $table->foreignId('cliente_anterior_id')->nullable()->constrained('clientes');
            $table->foreignId('visita_anterior_id')->nullable()->constrained('visitas');

            // Ubicación nueva
            $table->foreignId('bodega_nueva_id')->nullable()->constrained('bodegas');
            $table->foreignId('user_nuevo_id')->nullable()->constrained('users');
            $table->foreignId('nodo_nuevo_id')->nullable()->constrained('nodos');
            $table->foreignId('cliente_nuevo_id')->nullable()->constrained('clientes');
            $table->foreignId('visita_nuevo_id')->nullable()->constrained('visitas');
            $table->foreignId('user_id')->constrained('users'); // Quién realizó el movimiento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
