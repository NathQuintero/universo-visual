<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Modelo: Cliente
 * 
 * Los clientes son los pacientes de la óptica. Cada cliente puede tener
 * múltiples trabajos y múltiples fórmulas ópticas.
 * 
 * Relaciones:
 * - Un cliente tiene muchos trabajos (historial)
 * - Un cliente tiene muchas fórmulas (evolución visual)
 */
class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'document_type',
        'document_number',
        'phone',
        'email',
        'address',
        'birth_date',
        'whatsapp_authorized',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'whatsapp_authorized' => 'boolean',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /** Todos los trabajos de este cliente */
    public function works()
    {
        return $this->hasMany(Work::class)->orderByDesc('created_at');
    }

    /** Todas las fórmulas de este cliente (historial visual) */
    public function formulas()
    {
        return $this->hasMany(Formula::class)->orderByDesc('created_at');
    }

    // ==========================================
    // MÉTODOS ÚTILES
    // ==========================================

    /** Nombre completo: "María López González" */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /** Iniciales para el avatar: "ML" */
    public function getInitialsAttribute(): string
    {
        return strtoupper(
            substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1)
        );
    }

    /** Fórmula más reciente */
    public function latestFormula()
    {
        return $this->formulas()->latest()->first();
    }

    /** Trabajo más reciente */
    public function latestWork()
    {
        return $this->works()->latest()->first();
    }

    /** Trabajos activos (no entregados ni cancelados) */
    public function activeWorks()
    {
        return $this->works()->whereNotIn('status', ['delivered', 'cancelled']);
    }

    /** Saldo pendiente total (suma de todos los trabajos) */
    public function getTotalPendingBalanceAttribute(): float
    {
        $total = 0;
        foreach ($this->works as $work) {
            $total += $work->pending_balance;
        }
        return $total;
    }

    /** ¿Es su cumpleaños esta semana? */
    public function isBirthdayThisWeek(): bool
    {
        if (!$this->birth_date) return false;

        $today = Carbon::today();
        $birthday = $this->birth_date->copy()->year($today->year);
        
        return $birthday->between($today, $today->copy()->addDays(7));
    }

    /** ¿Es su cumpleaños hoy? */
    public function isBirthdayToday(): bool
    {
        if (!$this->birth_date) return false;
        return $this->birth_date->format('m-d') === Carbon::today()->format('m-d');
    }

    /** Días hasta su próximo cumpleaños */
    public function daysUntilBirthday(): ?int
    {
        if (!$this->birth_date) return null;

        $today = Carbon::today();
        $birthday = $this->birth_date->copy()->year($today->year);
        
        if ($birthday->isPast()) {
            $birthday->addYear();
        }

        return $today->diffInDays($birthday);
    }
}