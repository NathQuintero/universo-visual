<?php

/**
 * Migración de reparación
 *
 * Las 3 migraciones anteriores (create_employees, add_employee_id_to_works,
 * add_employee_id_to_payments) corrieron por accidente con el esqueleto vacío
 * de Artisan, dejando la BD inconsistente con su definición. Esta migración
 * idempotente añade las columnas que faltan en la BD actual sin tocar nada
 * que ya exista, y deja el esquema final coherente.
 *
 * En una instalación limpia (`migrate:fresh`), las 3 migraciones originales
 * — ya editadas — definen el esquema completo y esta repair queda como no-op.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reparar tabla employees: añadir name, phone, is_active si faltan
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'name')) {
                    $table->string('name', 100)->after('id');
                }
                if (!Schema::hasColumn('employees', 'phone')) {
                    $table->string('phone', 30)->nullable()->after('name');
                }
                if (!Schema::hasColumn('employees', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('phone');
                }
            });
        }

        // Reparar works: añadir employee_id si falta
        if (Schema::hasTable('works') && !Schema::hasColumn('works', 'employee_id')) {
            Schema::table('works', function (Blueprint $table) {
                $table->foreignId('employee_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('employees')
                    ->nullOnDelete();
            });
        }

        // Reparar payments: añadir employee_id si falta
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'employee_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreignId('employee_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('employees')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // No-op: la reversión correspondiente la hacen las migraciones originales.
    }
};
