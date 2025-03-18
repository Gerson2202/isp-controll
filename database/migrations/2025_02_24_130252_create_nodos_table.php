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
            $table->string('user')->default('GerApi');
            $table->string('pass')->default('12345678');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->integer('puerto_api')->default(8728); // Valor por defecto           
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
