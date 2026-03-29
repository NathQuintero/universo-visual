<?php

/**
 * Migración: Tabla de Laboratorios
 * 
 * Los laboratorios son las empresas externas que fabrican los lentes
 * y los montan en la montura. La óptica envía los trabajos al laboratorio
 * y los recibe de vuelta cuando están listos.
 * 
 * Ejemplos: Servioptica, Italiana Lentes, Visiónlab
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id(); // ID único del laboratorio
            
            // Información básica del laboratorio
            $table->string('name');              // Nombre: "Servioptica"
            $table->string('contact_name')       // Persona de contacto
                  ->nullable();
            $table->string('phone')              // Teléfono principal
                  ->nullable();
            $table->string('email')              // Correo electrónico
                  ->nullable();
            $table->string('city')               // Ciudad: "Bucaramanga"
                  ->nullable();
            $table->text('address')              // Dirección completa
                  ->nullable();
            
            // Estado del laboratorio
            $table->boolean('is_active')         // Si está activo o no
                  ->default(true);
            
            $table->timestamps(); // created_at y updated_at automáticos
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};