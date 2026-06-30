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
        Schema::create('gastos_recurrentes', function (Blueprint $table) {

            $table->id();
            $table->foreignId('categorias_gasto_id')
                ->constrained()
                ->restrictOnDelete();
            $table->string('concepto');
            $table->decimal('valor', 12, 2);
            $table->enum('frecuencia', [
                'mensual',
                'quincenal',
                'anual'
            ]);
            $table->integer('dia_ejecucion');
            $table->boolean('activo')
                ->default(true);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['fijo', 'variable'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasto_recurrentes');
    }
};
