<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Laboratorio
 * 
 * Los laboratorios son empresas externas que fabrican los lentes
 * y los montan en la montura. La óptica NO fabrica lentes.
 * 
 * Relaciones:
 * - Un laboratorio tiene muchos trabajos asignados
 */
class Laboratory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
        'city',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /** Todos los trabajos asignados a este laboratorio */
    public function works()
    {
        return $this->hasMany(Work::class);
    }

    // ==========================================
    // MÉTODOS ÚTILES
    // ==========================================

    /** Trabajos activos (no entregados ni cancelados) */
    public function activeWorks()
    {
        return $this->works()->whereNotIn('status', ['delivered', 'cancelled']);
    }

    /** Promedio de días de entrega de este laboratorio */
    public function averageDeliveryDays()
    {
        return $this->works()
            ->where('status', 'delivered')
            ->whereNotNull('actual_delivery')
            ->selectRaw('AVG(DATEDIFF(actual_delivery, created_at)) as avg_days')
            ->value('avg_days');
    }

    /** Porcentaje de cumplimiento (entregados a tiempo) */
    public function complianceRate()
    {
        $total = $this->works()->where('status', 'delivered')->count();
        if ($total === 0) return 100;

        $onTime = $this->works()
            ->where('status', 'delivered')
            ->whereNotNull('estimated_delivery')
            ->whereNotNull('actual_delivery')
            ->whereColumn('actual_delivery', '<=', 'estimated_delivery')
            ->count();

        return round(($onTime / $total) * 100);
    }
}