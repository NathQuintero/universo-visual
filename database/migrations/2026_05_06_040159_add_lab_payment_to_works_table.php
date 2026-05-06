<?php

/**
 * Migración: Pagos al laboratorio
 *
 * Cuando el laboratorio devuelve el lente fabricado (status `received`),
 * la óptica le debe el costo del lente. El laboratorio da 30 días de plazo
 * pero la óptica quiere pagarles cada 15 días.
 *
 * Campos nuevos en `works`:
 *   - lab_cost     : lo que cobra el laboratorio por el lente (decimal)
 *   - lab_paid_at  : cuándo le pagamos al laboratorio por este lente
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->decimal('lab_cost', 12, 2)->default(0)->after('price_total');
            $table->date('lab_paid_at')->nullable()->after('lab_cost');
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn(['lab_cost', 'lab_paid_at']);
        });
    }
};
