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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();  // Esto crea la columna 'id' con AUTO_INCREMENT y como clave primaria
            $table->string('nombre');
            $table->string('telefono')->nullable(); // El campo 'telefono' no debe tener AUTO_INCREMENT
            $table->string('cedula')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->string('direccion')->nullable();
            $table->string('ip')->nullable()->unique();
            $table->string('correo')->nullable();
            $table->string('punto_referencia')->nullable();
            $table->string('descripcion')->nullable();
            $table->foreignId('pool_id')->nullable()->constrained()->onDelete('cascade'); // Relación con el pool   
            $table->enum('estado', ['activo', 'cortado'])->default('activo');

            $table->timestamps();  // created_at, updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
