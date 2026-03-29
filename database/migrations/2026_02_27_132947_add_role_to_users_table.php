<?php

/**
 * Migración: Agregar campo de rol a la tabla de usuarios
 * 
 * Roles del sistema:
 * - admin: Administrador/Gerente (acceso total, ve reportes)
 * - seller: Vendedor (crea trabajos, gestiona clientes, NO ve reportes)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'seller'])  // Rol del usuario
                  ->default('seller')
                  ->after('email');
            $table->boolean('is_active')               // Si el usuario está activo
                  ->default(true)
                  ->after('role');
            $table->string('phone')                    // Teléfono del empleado
                  ->nullable()
                  ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'phone']);
        });
    }
};