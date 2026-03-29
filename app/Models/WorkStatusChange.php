<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Cambio de Estado de Trabajo
 * 
 * Registra cada cambio de estado de un trabajo para trazabilidad.
 * Ejemplo: Keren Q. cambió de "Registrado" a "Enviado al Laboratorio"
 * 
 * Relaciones:
 * - Pertenece a un trabajo
 * - Pertenece a un usuario (quién hizo el cambio)
 */
class WorkStatusChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'user_id',
        'from_status',
        'to_status',
        'notes',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /** Trabajo al que pertenece este cambio */
    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    /** Usuario que realizó el cambio */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}