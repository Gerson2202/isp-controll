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
        Schema::create('gastos', function (Blueprint $table) {

            $table->id();
            $table->foreignId('categorias_gasto_id')
                ->constrained()
                ->restrictOnDelete();
            $table->string('concepto');
            $table->decimal('valor', 12, 2);
            $table->date('fecha_gasto');
            $table->enum('tipo', [
                'fijo',
                'variable'
            ])->default('variable');
            $table->enum('estado', [
                'pendiente',
                'pagado'
            ])->default('pagado');
            $table->text('descripcion')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('numero_documento')->nullable();
            $table->string('beneficiario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
