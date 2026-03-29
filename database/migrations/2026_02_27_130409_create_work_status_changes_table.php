<?php

/**
 * Migración: Tabla de Cambios de Estado
 * 
 * Cada vez que un trabajo cambia de estado, se registra QUIÉN lo cambió,
 * CUÁNDO y a QUÉ estado pasó. Esto crea el historial completo del trabajo
 * y permite la trazabilidad que pidió el gerente.
 * 
 * Ejemplo de historial:
 * - 15/02 09:30 → Keren Q. cambió a "Registrado"
 * - 16/02 11:15 → Keren Q. cambió a "Enviado al laboratorio"
 * - 17/02 08:00 → Sistema cambió a "En proceso"
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_status_changes', function (Blueprint $table) {
            $table->id();
            
            // ¿De qué trabajo es este cambio?
            $table->foreignId('work_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // ¿Quién hizo el cambio?
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('restrict');
            
            // El estado anterior y el nuevo
            $table->string('from_status')        // Estado anterior: "registered"
                  ->nullable();                  // Null si es el primer registro
            $table->string('to_status');          // Nuevo estado: "sent_to_lab"
            
            // Observación opcional del cambio
            $table->text('notes')                // "Se envió con guía #12345"
                  ->nullable();
            
            $table->timestamps(); // created_at = cuándo se hizo el cambio
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_status_changes');
    }
};