<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo: Usuario del sistema
 * 
 * Representa a los empleados de la óptica que usan el sistema.
 * Roles: 'admin' (gerente/administrador) o 'seller' (vendedor).
 * 
 * Relaciones:
 * - Un usuario puede crear muchos trabajos
 * - Un usuario puede registrar muchos pagos
 * - Un usuario puede hacer muchos cambios de estado
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ==========================================
    // RELACIONES
    // ==========================================

    /** Trabajos creados por este usuario */
    public function works()
    {
        return $this->hasMany(Work::class);
    }

    /** Pagos registrados por este usuario */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /** Cambios de estado realizados por este usuario */
    public function statusChanges()
    {
        return $this->hasMany(WorkStatusChange::class);
    }

    // ==========================================
    // MÉTODOS ÚTILES
    // ==========================================

    /** ¿Es administrador? */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** ¿Es vendedor? */
    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    /** Nombre del rol en español */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrador',
            'seller' => 'Vendedor',
            default => 'Sin rol',
        };
    }
}