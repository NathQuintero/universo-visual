<?php

/**
 * Migración: Tabla de Fórmulas Ópticas
 * 
 * Cada vez que un cliente se hace un examen visual, se genera una nueva fórmula.
 * Se guardan TODAS las fórmulas para ver la evolución de la salud visual.
 * 
 * La fórmula tiene datos para cada ojo:
 * - OD = Ojo Derecho (oculus dexter)
 * - OI = Ojo Izquierdo (oculus sinister)
 * 
 * Cada ojo tiene: Esfera, Cilindro, Eje, ADD (adición), DNP (distancia naso-pupilar)
 * 
 * Los valores se guardan como decimal porque pueden ser negativos y con decimales.
 * Ejemplo: Esfera = -2.25, Cilindro = -0.75, Eje = 180
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulas', function (Blueprint $table) {
            $table->id(); // ID único de la fórmula
            
            // Relación: ¿de qué cliente es esta fórmula?
            $table->foreignId('client_id')       
                  ->constrained()                // Referencia a tabla clients
                  ->onDelete('cascade');          // Si se borra el cliente, se borran sus fórmulas
            
            // === OJO DERECHO (OD) ===
            $table->decimal('od_sphere', 6, 2)   // Esfera OD: ej -2.25
                  ->nullable();
            $table->decimal('od_cylinder', 6, 2) // Cilindro OD: ej -0.75
                  ->nullable();
            $table->integer('od_axis')           // Eje OD: 0 a 180 grados
                  ->nullable();
            $table->decimal('od_add', 6, 2)      // Adición OD: ej +2.00
                  ->nullable();
            $table->decimal('od_dnp', 5, 2)      // DNP OD en mm: ej 32.5
                  ->nullable();
            $table->decimal('od_prism', 6, 2)    // Prisma OD (poco común)
                  ->nullable();
            $table->string('od_prism_base')      // Base del prisma OD
                  ->nullable();
            
            // === OJO IZQUIERDO (OI) ===
            $table->decimal('oi_sphere', 6, 2)   // Esfera OI
                  ->nullable();
            $table->decimal('oi_cylinder', 6, 2) // Cilindro OI
                  ->nullable();
            $table->integer('oi_axis')           // Eje OI
                  ->nullable();
            $table->decimal('oi_add', 6, 2)      // Adición OI
                  ->nullable();
            $table->decimal('oi_dnp', 5, 2)      // DNP OI en mm
                  ->nullable();
            $table->decimal('oi_prism', 6, 2)    // Prisma OI
                  ->nullable();
            $table->string('oi_prism_base')      // Base del prisma OI
                  ->nullable();
            
            // Información adicional
            $table->date('exam_date')            // Fecha del examen visual
                  ->nullable();
            $table->text('notes')                // Observaciones del optómetra
                  ->nullable();
            
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulas');
    }
};