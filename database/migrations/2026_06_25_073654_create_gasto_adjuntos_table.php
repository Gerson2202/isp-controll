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
        Schema::create('gasto_adjuntos', function (Blueprint $table) {

            $table->id();

            $table->foreignId('gasto_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('archivo');

            $table->string('nombre_original');

            $table->string('mime_type');

            $table->unsignedBigInteger('size');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasto_adjuntos');
    }
};
