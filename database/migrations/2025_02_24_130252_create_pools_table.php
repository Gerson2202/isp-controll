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
        Schema::create('pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nodo_id')->nullable()->constrained()->onDelete('cascade');  // Relación con el nodo
            $table->string('nombre');    // Nombre del pool (ej. "cliente-pool")
            $table->string('descripcion')->nullable(); 
            $table->string('start_ip');  // IP de inicio del pool
            $table->string('end_ip');    // IP final del pool
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pools');
    }
};
