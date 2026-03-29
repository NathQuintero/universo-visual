<?php

/**
 * Migración: Tabla de Configuración
 * 
 * Tabla simple de clave-valor para guardar configuraciones del sistema.
 * Ejemplos:
 * - 'business_name' => 'Óptica Universo Visual'
 * - 'days_to_mark_delayed' => '5'
 * - 'days_to_remind_pickup' => '3'
 * - 'birthday_discount' => '15'
 * - 'whatsapp_number' => '573001234567'
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();     // Clave: 'business_name'
            $table->text('value')->nullable();    // Valor: 'Óptica Universo Visual'
            $table->string('description')         // Descripción legible
                  ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};