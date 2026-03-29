<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Formula;

/**
 * Seeder: Clientes de prueba con sus fórmulas ópticas
 * 
 * Crea clientes ficticios con datos realistas colombianos.
 * Cada cliente tiene al menos una fórmula óptica.
 * Algunos tienen múltiples fórmulas (historial visual).
 */
class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // CLIENTE 1: María López González
        // Cliente frecuente, 3 fórmulas en historial
        // =============================================
        $maria = Client::create([
            'first_name' => 'María',
            'last_name' => 'López González',
            'document_type' => 'CC',
            'document_number' => '63524891',
            'phone' => '3151234567',
            'email' => 'maria.lopez@email.com',
            'address' => 'Cra 25 #45-12, Cabecera, Bucaramanga',
            'birth_date' => '1985-03-15',
            'whatsapp_authorized' => true,
            'notes' => 'Cliente frecuente. Prefiere monturas Ray-Ban.',
        ]);

        // Fórmula más antigua (2024)
        Formula::create([
            'client_id' => $maria->id,
            'od_sphere' => -2.00, 'od_cylinder' => -0.50, 'od_axis' => 180,
            'od_add' => 1.75, 'od_dnp' => 32,
            'oi_sphere' => -1.50, 'oi_cylinder' => -0.25, 'oi_axis' => 175,
            'oi_add' => 1.75, 'oi_dnp' => 31,
            'exam_date' => '2024-06-10',
            'notes' => 'Primer examen en la óptica.',
        ]);

        // Fórmula intermedia (2025)
        Formula::create([
            'client_id' => $maria->id,
            'od_sphere' => -2.00, 'od_cylinder' => -0.75, 'od_axis' => 180,
            'od_add' => 2.00, 'od_dnp' => 32,
            'oi_sphere' => -1.75, 'oi_cylinder' => -0.50, 'oi_axis' => 175,
            'oi_add' => 2.00, 'oi_dnp' => 31,
            'exam_date' => '2025-03-20',
            'notes' => 'Aumento leve en cilindro y adición.',
        ]);

        // Fórmula actual (2026) — la más reciente
        Formula::create([
            'client_id' => $maria->id,
            'od_sphere' => -2.25, 'od_cylinder' => -0.75, 'od_axis' => 180,
            'od_add' => 2.00, 'od_dnp' => 32,
            'oi_sphere' => -1.75, 'oi_cylinder' => -0.50, 'oi_axis' => 175,
            'oi_add' => 2.00, 'oi_dnp' => 31,
            'exam_date' => '2026-02-15',
            'notes' => 'Leve aumento en esfera OD. Se recomienda progresivos.',
        ]);

        // =============================================
        // CLIENTE 2: Carlos Andrés Rueda
        // Cliente con saldo pendiente
        // =============================================
        $carlos = Client::create([
            'first_name' => 'Carlos Andrés',
            'last_name' => 'Rueda Patiño',
            'document_type' => 'CC',
            'document_number' => '91235678',
            'phone' => '3009876543',
            'email' => 'carlos.rueda@email.com',
            'address' => 'Calle 52 #28-10, Centro, Bucaramanga',
            'birth_date' => '1990-07-22',
            'whatsapp_authorized' => true,
        ]);

        Formula::create([
            'client_id' => $carlos->id,
            'od_sphere' => -4.00, 'od_cylinder' => -1.25, 'od_axis' => 90,
            'od_dnp' => 33,
            'oi_sphere' => -3.75, 'oi_cylinder' => -1.00, 'oi_axis' => 85,
            'oi_dnp' => 32,
            'exam_date' => '2026-02-20',
            'notes' => 'Miopía alta. Recomendar policarbonato o alto índice.',
        ]);

        // =============================================
        // CLIENTE 3: Ana Sofía Martínez
        // Cliente con trabajo demorado
        // =============================================
        $ana = Client::create([
            'first_name' => 'Ana Sofía',
            'last_name' => 'Martínez Vega',
            'document_type' => 'CC',
            'document_number' => '1098765432',
            'phone' => '3124567890',
            'email' => 'ana.martinez@email.com',
            'address' => 'Cra 33 #51-22, Sotomayor, Bucaramanga',
            'birth_date' => '1978-11-08',
            'whatsapp_authorized' => true,
            'notes' => 'Clienta desde 2024. Muy puntual en pagos.',
        ]);

        Formula::create([
            'client_id' => $ana->id,
            'od_sphere' => 1.50, 'od_cylinder' => -0.25, 'od_axis' => 60,
            'od_add' => 1.50, 'od_dnp' => 31,
            'oi_sphere' => 1.75, 'oi_cylinder' => -0.50, 'oi_axis' => 120,
            'oi_add' => 1.50, 'oi_dnp' => 30,
            'exam_date' => '2026-02-10',
        ]);

        // =============================================
        // CLIENTE 4: Pedro Ramírez Torres
        // Cliente nuevo
        // =============================================
        $pedro = Client::create([
            'first_name' => 'Pedro',
            'last_name' => 'Ramírez Torres',
            'document_type' => 'CC',
            'document_number' => '79456123',
            'phone' => '3167890123',
            'email' => null,
            'address' => 'Calle 30 #15-45, Provenza, Bucaramanga',
            'birth_date' => '1995-01-30',
            'whatsapp_authorized' => true,
        ]);

        Formula::create([
            'client_id' => $pedro->id,
            'od_sphere' => -1.50, 'od_cylinder' => -0.50, 'od_axis' => 170,
            'od_add' => 1.00, 'od_dnp' => 33,
            'oi_sphere' => -1.25, 'oi_cylinder' => -0.25, 'oi_axis' => 10,
            'oi_add' => 1.00, 'oi_dnp' => 32,
            'exam_date' => '2026-02-27',
            'notes' => 'Primera vez usando progresivos. Explicar adaptación.',
        ]);

        // =============================================
        // CLIENTE 5: Luisa Fernanda Díaz
        // =============================================
        $luisa = Client::create([
            'first_name' => 'Luisa Fernanda',
            'last_name' => 'Díaz Herrera',
            'document_type' => 'CC',
            'document_number' => '37890456',
            'phone' => '3186543210',
            'address' => 'Cra 21 #10-33, San Francisco, Bucaramanga',
            'birth_date' => '1988-09-14',
            'whatsapp_authorized' => true,
        ]);

        Formula::create([
            'client_id' => $luisa->id,
            'od_sphere' => -0.75, 'od_cylinder' => -0.25, 'od_axis' => 90,
            'od_dnp' => 31,
            'oi_sphere' => -1.00, 'oi_cylinder' => -0.50, 'oi_axis' => 85,
            'oi_dnp' => 30,
            'exam_date' => '2026-02-18',
        ]);

        // =============================================
        // CLIENTE 6: Laura Gómez (cumpleaños mañana)
        // =============================================
        $laura = Client::create([
            'first_name' => 'Laura',
            'last_name' => 'Gómez Suárez',
            'document_type' => 'CC',
            'document_number' => '1098234567',
            'phone' => '3201234567',
            'birth_date' => '1992-02-28',
            'whatsapp_authorized' => true,
            'notes' => 'Clienta frecuente. Le gustan monturas grandes.',
        ]);

        Formula::create([
            'client_id' => $laura->id,
            'od_sphere' => -3.00, 'od_cylinder' => -1.00, 'od_axis' => 45,
            'od_dnp' => 32,
            'oi_sphere' => -2.75, 'oi_cylinder' => -0.75, 'oi_axis' => 135,
            'oi_dnp' => 31,
            'exam_date' => '2025-12-05',
        ]);

        // =============================================
        // CLIENTE 7: Jorge Rodríguez (cumpleaños 1 Mar)
        // =============================================
        $jorge = Client::create([
            'first_name' => 'Jorge',
            'last_name' => 'Rodríguez Mantilla',
            'document_type' => 'CC',
            'document_number' => '88123456',
            'phone' => '3159876543',
            'birth_date' => '1980-03-01',
            'whatsapp_authorized' => true,
        ]);

        Formula::create([
            'client_id' => $jorge->id,
            'od_sphere' => 2.00, 'od_cylinder' => -0.50, 'od_axis' => 90,
            'od_add' => 2.50, 'od_dnp' => 34,
            'oi_sphere' => 2.25, 'oi_cylinder' => -0.75, 'oi_axis' => 85,
            'oi_add' => 2.50, 'oi_dnp' => 33,
            'exam_date' => '2026-01-15',
        ]);

        // =============================================
        // CLIENTE 8: Martha Vargas (cumpleaños 3 Mar)
        // =============================================
        Client::create([
            'first_name' => 'Martha',
            'last_name' => 'Vargas Ortiz',
            'document_type' => 'CC',
            'document_number' => '63987654',
            'phone' => '3174567890',
            'birth_date' => '1975-03-03',
            'whatsapp_authorized' => false,
        ]);
    }
}