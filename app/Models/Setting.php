<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Configuración del sistema
 * 
 * Tabla simple clave-valor para configuraciones globales.
 * Ejemplo: Setting::getValue('business_name') → "Óptica Universo Visual"
 */
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Obtener un valor de configuración por su clave.
     * Si no existe, devuelve el valor por defecto.
     * 
     * Uso: Setting::getValue('business_name', 'Mi Óptica')
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Guardar o actualizar un valor de configuración.
     * 
     * Uso: Setting::setValue('business_name', 'Óptica Universo Visual')
     */
    public static function setValue(string $key, mixed $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description]
        );
    }
}