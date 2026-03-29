<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Fórmula Óptica
 * 
 * La prescripción del optómetra. Tiene datos para OD (ojo derecho)
 * y OI (ojo izquierdo): Esfera, Cilindro, Eje, ADD, DNP.
 * 
 * Se guardan todas las fórmulas para ver la evolución visual del paciente.
 * 
 * Relaciones:
 * - Una fórmula pertenece a un cliente
 * - Una fórmula puede estar asociada a uno o más trabajos
 */
class Formula extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'od_sphere', 'od_cylinder', 'od_axis', 'od_add', 'od_dnp', 'od_prism', 'od_prism_base',
        'oi_sphere', 'oi_cylinder', 'oi_axis', 'oi_add', 'oi_dnp', 'oi_prism', 'oi_prism_base',
        'exam_date',
        'notes',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'od_sphere' => 'decimal:2',
        'od_cylinder' => 'decimal:2',
        'od_add' => 'decimal:2',
        'od_dnp' => 'decimal:2',
        'oi_sphere' => 'decimal:2',
        'oi_cylinder' => 'decimal:2',
        'oi_add' => 'decimal:2',
        'oi_dnp' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /** Cliente dueño de esta fórmula */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /** Trabajos que usan esta fórmula */
    public function works()
    {
        return $this->hasMany(Work::class);
    }

    // ==========================================
    // MÉTODOS ÚTILES
    // ==========================================

    /** Fórmula OD resumida: "-2.25 -0.75 x180" */
    public function getOdSummaryAttribute(): string
    {
        $parts = [];
        if ($this->od_sphere !== null) $parts[] = ($this->od_sphere >= 0 ? '+' : '') . number_format($this->od_sphere, 2);
        if ($this->od_cylinder !== null) $parts[] = number_format($this->od_cylinder, 2);
        if ($this->od_axis !== null) $parts[] = 'x' . $this->od_axis . '°';
        return implode(' ', $parts) ?: '—';
    }

    /** Fórmula OI resumida: "-1.75 -0.50 x175" */
    public function getOiSummaryAttribute(): string
    {
        $parts = [];
        if ($this->oi_sphere !== null) $parts[] = ($this->oi_sphere >= 0 ? '+' : '') . number_format($this->oi_sphere, 2);
        if ($this->oi_cylinder !== null) $parts[] = number_format($this->oi_cylinder, 2);
        if ($this->oi_axis !== null) $parts[] = 'x' . $this->oi_axis . '°';
        return implode(' ', $parts) ?: '—';
    }
}