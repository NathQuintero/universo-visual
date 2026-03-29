<?php

/**
 * Migración: Tabla de Clientes
 * 
 * Los clientes son los pacientes de la óptica. Cada cliente puede tener
 * múltiples trabajos (historial) y múltiples fórmulas ópticas a lo largo
 * del tiempo.
 * 
 * El campo 'whatsapp_authorized' es importante porque según la Ley 1581/2012
 * de protección de datos, necesitamos consentimiento para enviar mensajes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // ID único del cliente
            
            // Datos personales
            $table->string('first_name');        // Nombre: "María"
            $table->string('last_name');         // Apellido: "López González"
            $table->string('document_type')      // Tipo doc: CC, TI, CE, etc.
                  ->default('CC');
            $table->string('document_number')    // Número: "63524891"
                  ->unique();                    // No puede repetirse
            $table->string('phone')              // Teléfono/WhatsApp
                  ->nullable();
            $table->string('email')              // Correo electrónico
                  ->nullable();
            $table->text('address')              // Dirección de residencia
                  ->nullable();
            $table->date('birth_date')           // Fecha de nacimiento (para cumpleaños)
                  ->nullable();
            
            // Configuración de comunicación
            $table->boolean('whatsapp_authorized')  // ¿Autorizó WhatsApp?
                  ->default(false);
            
            // Notas adicionales
            $table->text('notes')                // Observaciones generales
                  ->nullable();
            
            $table->timestamps(); // created_at y updated_at automáticos
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};