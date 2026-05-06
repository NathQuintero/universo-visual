<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Empleada (Vendedora física de la óptica)
 *
 * Representa a las personas que atienden las ventas en el local
 * (Maira, Nelly, etc.). Comparten una sola cuenta de login del sistema
 * (la cuenta "trabajadora") y al registrar una venta o un pago seleccionan
 * cuál de ellas hizo la transacción.
 *
 * Solo el admin gestiona estas empleadas desde /trabajadoras.
 */
class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /** Trabajos atendidos por esta empleada */
    public function works()
    {
        return $this->hasMany(Work::class);
    }

    /** Pagos recibidos por esta empleada */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ==========================================
    // ACCESORES
    // ==========================================

    /** Iniciales para badges/avatars: "MN" */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name ?? ''));
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $second = mb_substr($parts[1] ?? '', 0, 1);
        return mb_strtoupper($first . $second);
    }
}
