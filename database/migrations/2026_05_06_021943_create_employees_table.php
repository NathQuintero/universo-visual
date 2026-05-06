<?php

/**
 * Migración: Tabla de Empleadas (Vendedoras)
 *
 * Las empleadas son las personas físicas que atienden las ventas en la óptica
 * (Maira, Nelly, etc.). NO son usuarios del sistema — comparten una sola cuenta
 * de login (la "trabajadora") y al momento de registrar una venta o un pago se
 * selecciona cuál de ellas hizo la transacción.
 *
 * Solo el admin gestiona esta tabla desde /trabajadoras.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
