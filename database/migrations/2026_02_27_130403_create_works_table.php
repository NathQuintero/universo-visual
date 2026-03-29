<?php

/**
 * Migración: Tabla de Trabajos
 * 
 * Esta es la tabla CENTRAL del sistema. Un "trabajo" es el pedido completo
 * del cliente en la óptica:
 * - El cliente compra montura + lentes, o
 * - El cliente trae su montura y pide que le pongan lentes
 * 
 * Cada trabajo tiene un código único tipo UV-2026-00234 que se usa para
 * el seguimiento. Este código es lo que el cliente usa para rastrear
 * sus gafas (como una guía de Servientrega).
 * 
 * ESTADOS POSIBLES del trabajo:
 * 1. registered    = Registrado (se creó la orden)
 * 2. sent_to_lab   = Enviado al Laboratorio
 * 3. in_process    = En Proceso (el lab está fabricando)
 * 4. received      = Recibido en Óptica (volvió del lab)
 * 5. ready         = Listo para Entregar
 * 6. delivered     = Entregado al Cliente
 * 7. cancelled     = Cancelado
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('works', function (Blueprint $table) {
            $table->id(); // ID único interno
            
            // Código de seguimiento público: UV-2026-00234
            $table->string('tracking_code')      
                  ->unique();                    // No puede repetirse
            
            // === RELACIONES ===
            $table->foreignId('client_id')       // ¿De qué cliente es?
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('laboratory_id')   // ¿A qué laboratorio se envía?
                  ->constrained()
                  ->onDelete('restrict');         // No borrar lab si tiene trabajos
            $table->foreignId('formula_id')      // ¿Qué fórmula se usa?
                  ->constrained()
                  ->onDelete('restrict');
            $table->foreignId('user_id')         // ¿Quién creó el trabajo? (vendedor/admin)
                  ->constrained()
                  ->onDelete('restrict');
            
            // === ESTADO ACTUAL ===
            $table->enum('status', [
                'registered',    // Registrado
                'sent_to_lab',   // Enviado al laboratorio
                'in_process',    // En proceso de fabricación
                'received',      // Recibido en óptica
                'ready',         // Listo para entregar
                'delivered',     // Entregado al cliente
                'cancelled'      // Cancelado
            ])->default('registered');
            
            // === MONTURA ===
            $table->enum('frame_type', [         // Tipo de montura
                'own',                           // Propia del cliente
                'purchased'                      // Comprada en la óptica
            ])->default('own');
            $table->string('frame_brand')        // Marca: "Ray-Ban"
                  ->nullable();
            $table->string('frame_reference')    // Referencia: "RB5154"
                  ->nullable();
            
            // === LENTE ===
            $table->enum('lens_type', [          // Tipo de visión
                'monofocal',                     // Una graduación
                'bifocal',                       // Dos graduaciones
                'progressive'                    // Graduación gradual
            ]);
            $table->enum('lens_material', [      // Material del lente
                'cr39',                          // Plástico estándar
                'polycarbonate',                 // Policarbonato
                'high_index',                    // Alto índice
                'trivex'                         // Trivex
            ])->default('cr39');
            
            // === TRATAMIENTOS (cada uno es un boolean) ===
            $table->boolean('treatment_antireflective')  // Antirreflejo
                  ->default(false);
            $table->boolean('treatment_photochromic')    // Fotocromático
                  ->default(false);
            $table->boolean('treatment_blue_filter')     // Filtro azul
                  ->default(false);
            $table->boolean('treatment_polarized')       // Polarizado
                  ->default(false);
            
            // === PRECIOS (en pesos colombianos) ===
            $table->decimal('price_lenses', 12, 2)   // Precio de los lentes
                  ->default(0);
            $table->decimal('price_frame', 12, 2)    // Precio de la montura
                  ->default(0);
            $table->decimal('price_consultation', 12, 2) // Precio consulta
                  ->default(0);
            $table->decimal('price_total', 12, 2)    // Total (calculado)
                  ->default(0);
            
            // === ETIQUETAS ===
            $table->boolean('is_urgent')         // ¿Es urgente? 🔥
                  ->default(false);
            $table->boolean('is_vip')            // ¿Cliente VIP? ⭐
                  ->default(false);
            $table->boolean('is_warranty')       // ¿Es garantía? 🔄
                  ->default(false);
            
            // === FECHAS ===
            $table->date('estimated_delivery')   // Fecha estimada de entrega
                  ->nullable();
            $table->date('actual_delivery')      // Fecha real de entrega
                  ->nullable();
            
            // === OBSERVACIONES ===
            $table->text('observations')         // Notas del vendedor
                  ->nullable();
            
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};