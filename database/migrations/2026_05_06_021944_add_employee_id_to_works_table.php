<?php

/**
 * Migración: Añadir employee_id a works
 *
 * Indica QUIÉN físicamente atendió la venta (Maira o Nelly), aparte del
 * user_id que sigue siendo el login del sistema (cuenta compartida).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->foreignId('employee_id')
                ->nullable()
                ->after('user_id')
                ->constrained('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
