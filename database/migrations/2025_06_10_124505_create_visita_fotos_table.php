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
       // database/migrations/xxxx_create_visita_fotos_table.php
        Schema::create('visita_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visita_id')->constrained()->onDelete('cascade');
            $table->string('ruta');
            $table->string('nombre_original');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visita_fotos');
    }
};
