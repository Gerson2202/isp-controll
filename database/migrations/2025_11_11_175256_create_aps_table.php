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
        Schema::create('aps', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');

            $table->string('ip_lan')->nullable()->unique();
            $table->string('ip_wan')->nullable();

            $table->integer('puerto_lan')->nullable();
            $table->integer('puerto_wan')->nullable();

            $table->string('ssid')->nullable();
            $table->string('clave')->nullable();

            $table->string('user_login')->nullable();
            $table->string('clave_login')->nullable();

            $table->integer('clientes_max')->nullable();
            $table->integer('frecuencia')->nullable();
            $table->string('ancho_canal', 10);
            $table->foreignId('pool_id')->nullable()->constrained()->onDelete('cascade'); // Relación con el pool   


            $table->enum('estado', ['activo', 'mantenimiento', 'caido'])->default('activo');
            // relación 1:1 con inventario
            $table->foreignId('inventario_id')
                ->unique()
                ->constrained('inventarios')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aps');
    }
};
