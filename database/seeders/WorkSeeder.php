<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Work;
use App\Models\WorkStatusChange;
use App\Models\Payment;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

/**
 * Seeder: Trabajos de prueba con pagos y cambios de estado
 * 
 * Crea trabajos en diferentes estados para demostrar todas
 * las funcionalidades del sistema. Incluye:
 * - Trabajo listo para entregar (María López)
 * - Trabajo en proceso (Carlos Rueda) 
 * - Trabajo demorado/urgente (Ana Sofía)
 * - Trabajo recién registrado (Pedro Ramírez)
 * - Trabajo listo (Luisa Díaz)
 * - Trabajo entregado (Laura Gómez)
 */
class WorkSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el usuario admin (lo necesitamos para asignar quién creó cada trabajo)
        $admin = User::where('role', 'admin')->first();

        // =============================================
        // TRABAJO 1: María López — LISTO PARA ENTREGAR
        // Progresivo, Servioptica, con saldo pendiente
        // =============================================
        $work1 = Work::create([
            'tracking_code' => 'UV-2026-00234',
            'client_id' => 1,       // María López
            'laboratory_id' => 1,   // Servioptica
            'formula_id' => 3,      // Fórmula más reciente de María
            'user_id' => $admin->id,
            'status' => 'ready',
            'frame_type' => 'own',
            'frame_brand' => 'Ray-Ban',
            'frame_reference' => 'RB5154',
            'lens_type' => 'progressive',
            'lens_material' => 'polycarbonate',
            'treatment_antireflective' => true,
            'treatment_photochromic' => true,
            'price_lenses' => 250000,
            'price_frame' => 0,      // Montura propia, no se cobra
            'price_consultation' => 0,
            'price_total' => 350000,
            'estimated_delivery' => '2026-02-22',
            'observations' => 'Cliente solicita que queden bien centrados. Montura propia en buen estado.',
            'created_at' => '2026-02-15 09:30:00',
        ]);

        // Historial de estados del trabajo 1
        $this->addStatusChange($work1, $admin, null, 'registered', '2026-02-15 09:30:00', 'Orden creada.');
        $this->addStatusChange($work1, $admin, 'registered', 'sent_to_lab', '2026-02-16 11:15:00', 'Enviado con guía interna #0234.');
        $this->addStatusChange($work1, $admin, 'sent_to_lab', 'in_process', '2026-02-17 08:00:00', 'Confirmado por el laboratorio.');
        $this->addStatusChange($work1, $admin, 'in_process', 'received', '2026-02-20 14:22:00', 'Recibido. Lentes en perfecto estado.');
        $this->addStatusChange($work1, $admin, 'received', 'ready', '2026-02-21 10:45:00', 'Verificado y listo para entregar.');

        // Pagos del trabajo 1 (abono parcial, queda saldo)
        Payment::create([
            'work_id' => $work1->id,
            'user_id' => $admin->id,
            'amount' => 200000,
            'method' => 'cash',
            'notes' => 'Abono al crear la orden.',
            'created_at' => '2026-02-15 09:30:00',
        ]);

        // =============================================
        // TRABAJO 2: Carlos Rueda — EN PROCESO
        // Monofocal, Italiana Lentes, con saldo pendiente
        // =============================================
        $work2 = Work::create([
            'tracking_code' => 'UV-2026-00233',
            'client_id' => 2,       // Carlos Rueda
            'laboratory_id' => 2,   // Italiana Lentes
            'formula_id' => 4,      // Fórmula de Carlos
            'user_id' => $admin->id,
            'status' => 'in_process',
            'frame_type' => 'purchased',
            'frame_brand' => 'Oakley',
            'frame_reference' => 'OX8046',
            'lens_type' => 'monofocal',
            'lens_material' => 'polycarbonate',
            'treatment_antireflective' => true,
            'price_lenses' => 180000,
            'price_frame' => 120000,
            'price_consultation' => 0,
            'price_total' => 300000,
            'estimated_delivery' => '2026-02-28',
            'observations' => 'Miopía alta, verificar espesor del lente.',
            'created_at' => '2026-02-24 10:00:00',
        ]);

        $this->addStatusChange($work2, $admin, null, 'registered', '2026-02-24 10:00:00');
        $this->addStatusChange($work2, $admin, 'registered', 'sent_to_lab', '2026-02-24 16:30:00');
        $this->addStatusChange($work2, $admin, 'sent_to_lab', 'in_process', '2026-02-25 09:00:00');

        Payment::create([
            'work_id' => $work2->id,
            'user_id' => $admin->id,
            'amount' => 150000,
            'method' => 'nequi',
            'notes' => 'Abono del 50%.',
            'created_at' => '2026-02-24 10:00:00',
        ]);

        // =============================================
        // TRABAJO 3: Ana Sofía — DEMORADO + URGENTE
        // Bifocal, Servioptica, 7 días sin actualización
        // =============================================
        $work3 = Work::create([
            'tracking_code' => 'UV-2026-00232',
            'client_id' => 3,       // Ana Sofía
            'laboratory_id' => 1,   // Servioptica
            'formula_id' => 5,      // Fórmula de Ana
            'user_id' => $admin->id,
            'status' => 'in_process',
            'frame_type' => 'purchased',
            'frame_brand' => 'Carolina Herrera',
            'frame_reference' => 'VHE882',
            'lens_type' => 'bifocal',
            'lens_material' => 'cr39',
            'treatment_antireflective' => true,
            'treatment_photochromic' => true,
            'is_urgent' => true,
            'price_lenses' => 200000,
            'price_frame' => 250000,
            'price_consultation' => 30000,
            'price_total' => 480000,
            'estimated_delivery' => '2026-02-24',
            'observations' => 'URGENTE: Cliente viaja el 1 de marzo. Necesita antes.',
            'created_at' => '2026-02-18 09:00:00',
        ]);

        $this->addStatusChange($work3, $admin, null, 'registered', '2026-02-18 09:00:00');
        $this->addStatusChange($work3, $admin, 'registered', 'sent_to_lab', '2026-02-18 14:00:00', 'Enviado como urgente.');
        $this->addStatusChange($work3, $admin, 'sent_to_lab', 'in_process', '2026-02-19 08:30:00');
        // NO hay más cambios — lleva 7+ días en "in_process" = DEMORADO

        Payment::create([
            'work_id' => $work3->id,
            'user_id' => $admin->id,
            'amount' => 480000,
            'method' => 'card',
            'notes' => 'Pagó total con tarjeta de crédito.',
            'created_at' => '2026-02-18 09:00:00',
        ]);

        // =============================================
        // TRABAJO 4: Pedro Ramírez — RECIÉN REGISTRADO
        // Progresivo, Visiónlab
        // =============================================
        $work4 = Work::create([
            'tracking_code' => 'UV-2026-00231',
            'client_id' => 4,       // Pedro Ramírez
            'laboratory_id' => 3,   // Visiónlab
            'formula_id' => 6,      // Fórmula de Pedro
            'user_id' => $admin->id,
            'status' => 'registered',
            'frame_type' => 'purchased',
            'frame_brand' => 'Tommy Hilfiger',
            'frame_reference' => 'TH1017',
            'lens_type' => 'progressive',
            'lens_material' => 'polycarbonate',
            'treatment_antireflective' => true,
            'treatment_blue_filter' => true,
            'price_lenses' => 280000,
            'price_frame' => 180000,
            'price_consultation' => 30000,
            'price_total' => 490000,
            'estimated_delivery' => '2026-03-06',
            'observations' => 'Primera vez con progresivos. Explicar período de adaptación.',
            'created_at' => Carbon::today()->format('Y-m-d') . ' 09:15:00',
        ]);

        $this->addStatusChange($work4, $admin, null, 'registered', Carbon::today()->format('Y-m-d') . ' 09:15:00', 'Cliente nuevo. Primera orden.');

        Payment::create([
            'work_id' => $work4->id,
            'user_id' => $admin->id,
            'amount' => 250000,
            'method' => 'transfer',
            'notes' => 'Transferencia Bancolombia.',
            'created_at' => Carbon::today()->format('Y-m-d') . ' 09:15:00',
        ]);

        // =============================================
        // TRABAJO 5: Luisa Díaz — LISTO PARA ENTREGAR
        // Monofocal, Italiana Lentes, pagado completo
        // =============================================
        $work5 = Work::create([
            'tracking_code' => 'UV-2026-00230',
            'client_id' => 5,       // Luisa Díaz
            'laboratory_id' => 2,   // Italiana Lentes
            'formula_id' => 7,      // Fórmula de Luisa
            'user_id' => $admin->id,
            'status' => 'ready',
            'frame_type' => 'own',
            'frame_brand' => 'Guess',
            'frame_reference' => 'GU2700',
            'lens_type' => 'monofocal',
            'lens_material' => 'cr39',
            'treatment_antireflective' => true,
            'price_lenses' => 120000,
            'price_frame' => 0,
            'price_consultation' => 0,
            'price_total' => 120000,
            'estimated_delivery' => '2026-02-25',
            'observations' => 'Montura propia en excelente estado.',
            'created_at' => '2026-02-20 11:00:00',
        ]);

        $this->addStatusChange($work5, $admin, null, 'registered', '2026-02-20 11:00:00');
        $this->addStatusChange($work5, $admin, 'registered', 'sent_to_lab', '2026-02-21 09:00:00');
        $this->addStatusChange($work5, $admin, 'sent_to_lab', 'in_process', '2026-02-22 08:00:00');
        $this->addStatusChange($work5, $admin, 'in_process', 'received', '2026-02-25 15:00:00');
        $this->addStatusChange($work5, $admin, 'received', 'ready', '2026-02-26 10:00:00');

        Payment::create([
            'work_id' => $work5->id,
            'user_id' => $admin->id,
            'amount' => 120000,
            'method' => 'cash',
            'notes' => 'Pagó total en efectivo.',
            'created_at' => '2026-02-20 11:00:00',
        ]);

        // =============================================
        // TRABAJO 6: Laura Gómez — ENTREGADO
        // Para mostrar en historial
        // =============================================
        $work6 = Work::create([
            'tracking_code' => 'UV-2026-00215',
            'client_id' => 6,       // Laura Gómez
            'laboratory_id' => 1,   // Servioptica
            'formula_id' => 8,      // Fórmula de Laura
            'user_id' => $admin->id,
            'status' => 'delivered',
            'frame_type' => 'purchased',
            'frame_brand' => 'Dolce & Gabbana',
            'frame_reference' => 'DG3299',
            'lens_type' => 'monofocal',
            'lens_material' => 'high_index',
            'treatment_antireflective' => true,
            'treatment_photochromic' => true,
            'price_lenses' => 320000,
            'price_frame' => 350000,
            'price_consultation' => 30000,
            'price_total' => 700000,
            'estimated_delivery' => '2026-02-20',
            'actual_delivery' => '2026-02-19',
            'created_at' => '2026-02-12 10:00:00',
        ]);

        $this->addStatusChange($work6, $admin, null, 'registered', '2026-02-12 10:00:00');
        $this->addStatusChange($work6, $admin, 'registered', 'sent_to_lab', '2026-02-12 15:00:00');
        $this->addStatusChange($work6, $admin, 'sent_to_lab', 'in_process', '2026-02-13 08:00:00');
        $this->addStatusChange($work6, $admin, 'in_process', 'received', '2026-02-18 14:00:00');
        $this->addStatusChange($work6, $admin, 'received', 'ready', '2026-02-18 16:00:00');
        $this->addStatusChange($work6, $admin, 'ready', 'delivered', '2026-02-19 11:00:00', 'Entregado. Cliente satisfecha.');

        Payment::create([
            'work_id' => $work6->id,
            'user_id' => $admin->id,
            'amount' => 400000,
            'method' => 'card',
            'created_at' => '2026-02-12 10:00:00',
        ]);
        Payment::create([
            'work_id' => $work6->id,
            'user_id' => $admin->id,
            'amount' => 300000,
            'method' => 'nequi',
            'notes' => 'Pago del saldo al recoger.',
            'created_at' => '2026-02-19 11:00:00',
        ]);

        // =============================================
        // TRABAJO 7: Jorge Rodríguez — EN LABORATORIO
        // =============================================
        $work7 = Work::create([
            'tracking_code' => 'UV-2026-00229',
            'client_id' => 7,       // Jorge Rodríguez
            'laboratory_id' => 4,   // LensMax
            'formula_id' => 9,      // Fórmula de Jorge
            'user_id' => $admin->id,
            'status' => 'sent_to_lab',
            'frame_type' => 'purchased',
            'frame_brand' => 'Nike',
            'frame_reference' => 'NK7130',
            'lens_type' => 'progressive',
            'lens_material' => 'polycarbonate',
            'treatment_antireflective' => true,
            'is_vip' => true,
            'price_lenses' => 300000,
            'price_frame' => 200000,
            'price_consultation' => 0,
            'price_total' => 500000,
            'estimated_delivery' => '2026-03-03',
            'created_at' => '2026-02-25 14:00:00',
        ]);

        $this->addStatusChange($work7, $admin, null, 'registered', '2026-02-25 14:00:00');
        $this->addStatusChange($work7, $admin, 'registered', 'sent_to_lab', '2026-02-26 09:00:00');

        Payment::create([
            'work_id' => $work7->id,
            'user_id' => $admin->id,
            'amount' => 500000,
            'method' => 'transfer',
            'notes' => 'Pagó total por transferencia.',
            'created_at' => '2026-02-25 14:00:00',
        ]);
    }

    /**
     * Método auxiliar para crear un cambio de estado
     * Evita repetir código en cada trabajo
     */
    private function addStatusChange(
        Work $work, 
        User $user, 
        ?string $from, 
        string $to, 
        string $date, 
        ?string $notes = null
    ): void {
        WorkStatusChange::create([
            'work_id' => $work->id,
            'user_id' => $user->id,
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}