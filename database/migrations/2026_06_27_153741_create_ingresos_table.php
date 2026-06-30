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
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            
            // Relación OPCIONAL con visitas
            $table->foreignId('visita_id')
                ->nullable()
                ->constrained('visitas')
                ->nullOnDelete();
            
            // Datos del ingreso
            $table->string('concepto');
            $table->decimal('monto', 12, 2);
            $table->date('fecha_ingreso');
            $table->enum('tipo', [
                'instalacion',
                'servicio_extra',
                'venta_producto',
                'consultoria',
                'otro'
            ])->default('otro');
            
            // Relación OPCIONAL con clientes
            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->nullOnDelete();
            
            $table->enum('estado', ['registrado', 'confirmado', 'anulado'])->default('registrado');
            $table->text('descripcion')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('metodo_pago')->nullable();
            
            // Usuario que registra
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['fecha_ingreso', 'tipo']);
            $table->index('visita_id');
            $table->index('cliente_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};