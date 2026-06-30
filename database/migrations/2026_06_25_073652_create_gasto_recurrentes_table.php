<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos_recurrentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorias_gasto_id')->constrained();
            $table->string('concepto');
            $table->decimal('valor', 12, 2);
            $table->enum('frecuencia', ['mensual', 'quincenal', 'anual']);
            $table->integer('dia_ejecucion');
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['fijo', 'variable'])->nullable();

            // 🔥 CAMPOS PARA CONTROL DE PAGO MENSUAL
            $table->year('ano')->nullable();  // Año del pago
            $table->unsignedTinyInteger('mes')->nullable(); // Mes del pago (1-12)
            $table->date('fecha_pago')->nullable(); // Fecha exacta del pago
            $table->boolean('pagado')->default(false); // Estado del pago

            $table->timestamps();

            // Índice para búsquedas rápidas
            $table->index(['ano', 'mes', 'concepto']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos_recurrentes');
    }
};
