<?php

/**
 * Migración: Tabla de Pagos (Abonos)
 * 
 * Un trabajo puede tener MÚLTIPLES pagos parciales (abonos).
 * Ejemplo: Total $350.000
 * - Abono 1: $200.000 (al crear el trabajo)
 * - Abono 2: $100.000 (a los 3 días)
 * - Abono 3: $50.000 (al recoger las gafas)
 * 
 * El saldo pendiente se calcula: Total - Suma de todos los abonos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // ¿De qué trabajo es este pago?
            $table->foreignId('work_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // ¿Quién registró el pago?
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('restrict');
            
            // Datos del pago
            $table->decimal('amount', 12, 2);    // Monto: 200000.00
            $table->enum('method', [              // Método de pago
                'cash',                           // Efectivo
                'card',                           // Tarjeta
                'transfer',                       // Transferencia
                'nequi',                          // Nequi
                'daviplata',                      // Daviplata
                'other'                           // Otro
            ])->default('cash');
            $table->text('notes')                // Observaciones
                  ->nullable();
            
            $table->timestamps(); // created_at = fecha del pago
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};