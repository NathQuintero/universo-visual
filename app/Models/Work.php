<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Modelo: Trabajo
 * 
 * El modelo CENTRAL del sistema. Un "trabajo" es el pedido completo
 * del cliente: montura + lentes + fórmula + laboratorio + precios.
 * 
 * Relaciones:
 * - Un trabajo pertenece a un cliente
 * - Un trabajo pertenece a un laboratorio
 * - Un trabajo pertenece a una fórmula
 * - Un trabajo tiene muchos pagos (abonos)
 * - Un trabajo tiene muchos cambios de estado (historial)
 */
class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_code',
        'client_id',
        'laboratory_id',
        'formula_id',
        'user_id',
        'status',
        'frame_type',
        'frame_brand',
        'frame_reference',
        'lens_type',
        'lens_material',
        'treatment_antireflective',
        'treatment_photochromic',
        'treatment_blue_filter',
        'treatment_polarized',
        'price_lenses',
        'price_frame',
        'price_consultation',
        'price_total',
        'is_urgent',
        'is_vip',
        'is_warranty',
        'estimated_delivery',
        'actual_delivery',
        'observations',
    ];

    protected $casts = [
        'treatment_antireflective' => 'boolean',
        'treatment_photochromic' => 'boolean',
        'treatment_blue_filter' => 'boolean',
        'treatment_polarized' => 'boolean',
        'is_urgent' => 'boolean',
        'is_vip' => 'boolean',
        'is_warranty' => 'boolean',
        'estimated_delivery' => 'date',
        'actual_delivery' => 'date',
        'price_lenses' => 'decimal:2',
        'price_frame' => 'decimal:2',
        'price_consultation' => 'decimal:2',
        'price_total' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /** Cliente dueño de este trabajo */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /** Laboratorio asignado */
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    /** Fórmula óptica usada */
    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    /** Usuario que creó el trabajo */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Todos los pagos/abonos de este trabajo */
    public function payments()
    {
        return $this->hasMany(Payment::class)->orderByDesc('created_at');
    }

    /** Historial de cambios de estado */
    public function statusChanges()
    {
        return $this->hasMany(WorkStatusChange::class)->orderBy('created_at');
    }

    // ==========================================
    // MÉTODOS DE CÁLCULO
    // ==========================================

    /** Total abonado (suma de todos los pagos) */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /** Saldo pendiente = Total - Abonado */
    public function getPendingBalanceAttribute(): float
    {
        return (float) $this->price_total - $this->total_paid;
    }

    /** ¿Está completamente pagado? */
    public function isPaidInFull(): bool
    {
        return $this->pending_balance <= 0;
    }

    // ==========================================
    // MÉTODOS DE ESTADO
    // ==========================================

    /** Días transcurridos desde la creación */
    public function getDaysElapsedAttribute(): int
    {
        return (int) Carbon::parse($this->created_at)->diffInDays(Carbon::today());
    }

    /** ¿Está demorado? (más de 5 días sin cambiar de estado) */
    public function getIsDelayedAttribute(): bool
    {
        if (in_array($this->status, ['delivered', 'cancelled'])) return false;
        
        $lastChange = $this->statusChanges()->latest()->first();
        if (!$lastChange) return $this->days_elapsed > 5;
        
        return Carbon::parse($lastChange->created_at)->diffInDays(Carbon::today()) > 5;
    }

    /** ¿Está listo y sin recoger hace más de 3 días? */
    public function getIsWaitingPickupAttribute(): bool
    {
        if ($this->status !== 'ready') return false;
        
        $lastChange = $this->statusChanges()
            ->where('to_status', 'ready')
            ->latest()
            ->first();
            
        if (!$lastChange) return false;
        
        return Carbon::parse($lastChange->created_at)->diffInDays(Carbon::today()) > 3;
    }

    /** Nombre del estado en español */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'registered' => 'Registrado',
            'sent_to_lab' => 'Enviado al Laboratorio',
            'in_process' => 'En Proceso',
            'received' => 'Recibido en Óptica',
            'ready' => 'Listo para Entregar',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
            default => 'Desconocido',
        };
    }

    /** Emoji del estado */
    public function getStatusEmojiAttribute(): string
    {
        return match($this->status) {
            'registered' => '📝',
            'sent_to_lab' => '📦',
            'in_process' => '🔬',
            'received' => '📬',
            'ready' => '✅',
            'delivered' => '🎉',
            'cancelled' => '❌',
            default => '❓',
        };
    }

    /** Color CSS del estado (para badges) */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'registered' => 'blue',
            'sent_to_lab' => 'yellow',
            'in_process' => 'orange',
            'received' => 'purple',
            'ready' => 'green',
            'delivered' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /** Mensaje de seguimiento para el cliente */
    public function getTrackingMessageAttribute(): string
    {
        return match($this->status) {
            'registered' => 'Tu orden ha sido registrada. Pronto enviaremos tus gafas al laboratorio.',
            'sent_to_lab' => 'Tus gafas fueron enviadas al laboratorio para la fabricación de los lentes.',
            'in_process' => 'Tus lentes están siendo fabricados en el laboratorio.',
            'received' => 'Tus gafas ya llegaron del laboratorio. Estamos verificando que todo esté perfecto.',
            'ready' => '¡Tus gafas están listas! Ya puedes pasar a recogerlas a la óptica.',
            'delivered' => '¡Tus gafas fueron entregadas! Gracias por confiar en Universo Visual.',
            'cancelled' => 'Este trabajo fue cancelado.',
            default => 'Estado no disponible.',
        };
    }

    // ==========================================
    // MÉTODOS DE LENTE
    // ==========================================

    /** Nombre del tipo de lente en español */
    public function getLensTypeNameAttribute(): string
    {
        return match($this->lens_type) {
            'monofocal' => 'Monofocal',
            'bifocal' => 'Bifocal',
            'progressive' => 'Progresivo',
            default => 'No definido',
        };
    }

    /** Nombre del material en español */
    public function getLensMaterialNameAttribute(): string
    {
        return match($this->lens_material) {
            'cr39' => 'CR-39',
            'polycarbonate' => 'Policarbonato',
            'high_index' => 'Alto Índice',
            'trivex' => 'Trivex',
            default => 'No definido',
        };
    }

    /** Lista de tratamientos activos */
    public function getTreatmentsListAttribute(): array
    {
        $treatments = [];
        if ($this->treatment_antireflective) $treatments[] = 'Antirreflejo';
        if ($this->treatment_photochromic) $treatments[] = 'Fotocromático';
        if ($this->treatment_blue_filter) $treatments[] = 'Filtro Azul';
        if ($this->treatment_polarized) $treatments[] = 'Polarizado';
        return $treatments;
    }

    /** Tratamientos como texto: "Antirreflejo, Fotocromático" */
    public function getTreatmentsTextAttribute(): string
    {
        $list = $this->treatments_list;
        return count($list) > 0 ? implode(', ', $list) : 'Sin tratamientos';
    }

    // ==========================================
    // GENERACIÓN DE CÓDIGO
    // ==========================================

    /**
     * Genera un código de seguimiento único: UV-2026-00234
     * Se llama automáticamente antes de crear un trabajo nuevo.
     */
    public static function generateTrackingCode(): string
    {
        $year = date('Y');
        $prefix = "UV-{$year}-";
        
        // Buscar el último código del año actual
        $lastWork = static::where('tracking_code', 'like', $prefix . '%')
            ->orderByDesc('tracking_code')
            ->first();

        if ($lastWork) {
            // Extraer el número y sumar 1
            $lastNumber = (int) str_replace($prefix, '', $lastWork->tracking_code);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}