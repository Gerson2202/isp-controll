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
        Schema::create('nodos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ip');
            $table->string('user')->default('ger_api_xyz');
            $table->string('pass')->default('Yt7!r9k@Dq#W1z');
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->integer('puerto_api')->default(8233); // Valor por defecto           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodos');
    }
};
