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
        Schema::create('foto__tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
        $table->string('foto');  // Aquí se almacena el path o nombre del archivo de la foto
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto__tickets');
    }
};
