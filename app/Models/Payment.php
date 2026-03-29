<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Pago (Abono)
 * 
 * Cada pago parcial que hace el cliente sobre un trabajo.
 * Un trabajo puede tener múltiples abonos.
 * 
 * Relaciones:
 * - Pertenece a un trabajo
 * - Pertenece a un usuario (quién registró el pago)
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'user_id',
        'amount',
        'method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /** Trabajo al que pertenece este pago */
    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    /** Usuario que registró el pago */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================
    // MÉTODOS ÚTILES
    // ==========================================

    /** Nombre del método de pago en español */
    public function getMethodNameAttribute(): string
    {
        return match($this->method) {
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
            'nequi' => 'Nequi',
            'daviplata' => 'Daviplata',
            'other' => 'Otro',
            default => 'No definido',
        };
    }
}