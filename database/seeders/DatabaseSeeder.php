<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder Principal
 * 
 * Este es el archivo que ejecuta todos los seeders en orden.
 * El orden importa porque hay dependencias entre tablas:
 * 1. Primero usuarios (se necesitan para asignar quién creó los trabajos)
 * 2. Luego laboratorios (se necesitan para asignar a trabajos)
 * 3. Luego clientes + fórmulas (se necesitan para crear trabajos)
 * 4. Finalmente trabajos + pagos + estados
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. CREAR USUARIOS DEL SISTEMA
        // =============================================
        
        // Usuario Administrador (Keren — gerente de la óptica)
        User::create([
            'name' => 'Keren Quintero',
            'email' => 'admin@universovisual.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'phone' => '3001234567',
        ]);

        // Usuario Vendedor (empleado de prueba)
        User::create([
            'name' => 'Camila Vargas',
            'email' => 'vendedor@universovisual.com',
            'password' => Hash::make('vendedor123'),
            'role' => 'seller',
            'is_active' => true,
            'phone' => '3109876543',
        ]);

        // =============================================
        // 2. CREAR CONFIGURACIÓN INICIAL
        // =============================================
        Setting::setValue('business_name', 'Óptica Universo Visual', 'Nombre del negocio');
        Setting::setValue('business_phone', '6071234567', 'Teléfono del negocio');
        Setting::setValue('business_address', 'C.C. La Isla, Local 205, Bucaramanga', 'Dirección');
        Setting::setValue('business_whatsapp', '573001234567', 'WhatsApp del negocio');
        Setting::setValue('days_to_mark_delayed', '5', 'Días para marcar trabajo como demorado');
        Setting::setValue('days_to_remind_pickup', '3', 'Días para recordar recogida');
        Setting::setValue('birthday_discount', '15', 'Porcentaje de descuento por cumpleaños');

        // =============================================
        // 3. EJECUTAR LOS DEMÁS SEEDERS EN ORDEN
        // =============================================
        $this->call([
            LaboratorySeeder::class,    // Laboratorios
            ClientSeeder::class,        // Clientes + Fórmulas
            WorkSeeder::class,          // Trabajos + Pagos + Estados
        ]);
    }
}