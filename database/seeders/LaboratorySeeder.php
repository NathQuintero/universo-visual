<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laboratory;

/**
 * Seeder: Laboratorios de prueba
 * 
 * Crea los laboratorios aliados de la óptica.
 * Estos son laboratorios reales de Bucaramanga y Colombia
 * que trabajan con ópticas.
 */
class LaboratorySeeder extends Seeder
{
    public function run(): void
    {
        $laboratories = [
            [
                'name' => 'Servioptica',
                'contact_name' => 'Carlos Mendoza',
                'phone' => '607-123-4567',
                'email' => 'pedidos@servioptica.com',
                'city' => 'Bucaramanga',
                'address' => 'Cra 27 #36-42, Bucaramanga',
                'is_active' => true,
            ],
            [
                'name' => 'Italiana Lentes',
                'contact_name' => 'Andrea Ruiz',
                'phone' => '607-765-4321',
                'email' => 'contacto@italianalentes.com',
                'city' => 'Bucaramanga',
                'address' => 'Calle 45 #23-15, Bucaramanga',
                'is_active' => true,
            ],
            [
                'name' => 'Visiónlab',
                'contact_name' => 'Roberto Díaz',
                'phone' => '601-555-8888',
                'email' => 'info@visionlab.co',
                'city' => 'Bogotá',
                'address' => 'Av Calle 72 #10-25, Bogotá',
                'is_active' => true,
            ],
            [
                'name' => 'LensMax',
                'contact_name' => 'Patricia Gómez',
                'phone' => '607-444-3322',
                'email' => 'ventas@lensmax.co',
                'city' => 'Bucaramanga',
                'address' => 'Cra 15 #48-30, Bucaramanga',
                'is_active' => true,
            ],
        ];

        foreach ($laboratories as $lab) {
            Laboratory::create($lab);
        }
    }
}