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
        Schema::create('consumible_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumible_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad')->default(0);
            $table->foreignId('bodega_id')->nullable()->constrained('bodegas')->onDelete('set null');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('nodo_id')->nullable()->constrained('nodos')->onDelete('set null');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('visita_id')->nullable()->constrained('visitas')->onDelete('set null');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumible_stock');
    }
};
