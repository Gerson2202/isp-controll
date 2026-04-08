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
        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->nullable()->after('descripcion');

            $table->integer('rafaga_max_bajada')->nullable()->after('velocidad_subida');
            $table->integer('rafaga_max_subida')->nullable();

            $table->integer('velocidad_media_bajada')->nullable();
            $table->integer('velocidad_media_subida')->nullable();

            $table->integer('tiempo_rafaga_bajada')->nullable();
            $table->integer('tiempo_rafaga_subida')->nullable();

            $table->integer('prioridad')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
};
