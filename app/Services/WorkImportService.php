<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Formula;
use App\Models\Laboratory;
use App\Models\Payment;
use App\Models\Work;
use App\Models\WorkStatusChange;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class WorkImportService
{
    // Columnas del Excel exportado (0-indexed, datos empiezan en fila 5, index 4)
    const COL_TRACKING = 0;
    const COL_NOMBRE = 1;
    const COL_APELLIDO = 2;
    const COL_TIPO_DOC = 3;
    const COL_NRO_DOC = 4;
    const COL_TELEFONO = 5;
    const COL_EMAIL = 6;
    const COL_DIRECCION = 7;
    const COL_LABORATORIO = 8;
    const COL_TIPO_LENTE = 9;
    const COL_MATERIAL = 10;
    const COL_ANTIRREFLEJO = 11;
    const COL_FOTOCROMATICO = 12;
    const COL_FILTRO_AZUL = 13;
    const COL_POLARIZADO = 14;
    const COL_MONTURA = 15;
    const COL_OD_ESFERA = 16;
    const COL_OD_CILINDRO = 17;
    const COL_OD_EJE = 18;
    const COL_OD_ADD = 19;
    const COL_OI_ESFERA = 20;
    const COL_OI_CILINDRO = 21;
    const COL_OI_EJE = 22;
    const COL_OI_ADD = 23;
    const COL_PRECIO_LENTES = 24;
    const COL_PRECIO_MONTURA = 25;
    const COL_PRECIO_TOTAL = 26;
    const COL_ABONO = 27;
    const COL_OBSERVACIONES = 28;

    protected array $rows = [];

    public function loadFile(string $filePath): self
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $this->rows = $sheet->toArray(null, true, true, false);
        return $this;
    }

    /**
     * Analizar el archivo sin importar nada.
     * Retorna resumen de lo que se va a hacer.
     */
    public function analyze(): array
    {
        $dataRows = $this->getDataRows();

        $existing = [];
        $newWorks = [];
        $newClients = [];
        $existingClients = [];
        $errors = [];
        $seenDocs = [];

        foreach ($dataRows as $idx => $row) {
            $excelRow = $idx + 5; // Número de fila visible en Excel
            $tracking = trim($row[self::COL_TRACKING] ?? '');
            $nombre = trim($row[self::COL_NOMBRE] ?? '');
            $documento = trim($row[self::COL_NRO_DOC] ?? '');
            $tipoLente = trim($row[self::COL_TIPO_LENTE] ?? '');
            $precioTotal = $this->parseNumber($row[self::COL_PRECIO_TOTAL] ?? '');

            // Fila vacía → ignorar
            if (empty($nombre) && empty($documento) && empty($tipoLente)) {
                continue;
            }

            // ¿Ya existe por tracking code?
            if (!empty($tracking) && Work::where('tracking_code', $tracking)->exists()) {
                $existing[] = $tracking;
                continue;
            }

            // Validaciones
            $rowErrors = [];
            if (empty($nombre)) $rowErrors[] = "falta nombre del cliente";
            if (empty($documento)) $rowErrors[] = "falta número de documento";
            if (empty($tipoLente)) $rowErrors[] = "falta tipo de lente";
            if ($precioTotal <= 0) $rowErrors[] = "falta precio total";

            if (!empty($rowErrors)) {
                $errors[] = "Fila {$excelRow}: " . implode(', ', $rowErrors);
                continue;
            }

            // Clasificar cliente
            if (!in_array($documento, $seenDocs)) {
                $seenDocs[] = $documento;
                $clientExists = Client::where('document_number', $documento)->exists();
                if ($clientExists) {
                    $existingClients[] = $nombre . ' ' . trim($row[self::COL_APELLIDO] ?? '') . " (CC {$documento})";
                } else {
                    $newClients[] = $nombre . ' ' . trim($row[self::COL_APELLIDO] ?? '') . " (CC {$documento})";
                }
            }

            $newWorks[] = [
                'row' => $excelRow,
                'client' => $nombre . ' ' . trim($row[self::COL_APELLIDO] ?? ''),
                'lens' => $tipoLente,
                'total' => $precioTotal,
            ];
        }

        return [
            'total_rows' => count($dataRows),
            'existing' => $existing,
            'existing_count' => count($existing),
            'new_works' => $newWorks,
            'new_works_count' => count($newWorks),
            'new_clients' => $newClients,
            'new_clients_count' => count($newClients),
            'existing_clients' => $existingClients,
            'existing_clients_count' => count($existingClients),
            'errors' => $errors,
            'errors_count' => count($errors),
        ];
    }

    /**
     * Ejecutar la importación real.
     */
    public function import(): array
    {
        $dataRows = $this->getDataRows();
        $imported = 0;
        $clientsCreated = 0;
        $errors = [];

        foreach ($dataRows as $idx => $row) {
            $excelRow = $idx + 5;
            $tracking = trim($row[self::COL_TRACKING] ?? '');
            $nombre = trim($row[self::COL_NOMBRE] ?? '');
            $apellido = trim($row[self::COL_APELLIDO] ?? '');
            $documento = trim($row[self::COL_NRO_DOC] ?? '');
            $tipoLente = trim($row[self::COL_TIPO_LENTE] ?? '');
            $precioTotal = $this->parseNumber($row[self::COL_PRECIO_TOTAL] ?? '');

            // Fila vacía
            if (empty($nombre) && empty($documento) && empty($tipoLente)) {
                continue;
            }

            // Ya existe
            if (!empty($tracking) && Work::where('tracking_code', $tracking)->exists()) {
                continue;
            }

            // Validar mínimos
            if (empty($nombre) || empty($documento) || empty($tipoLente) || $precioTotal <= 0) {
                $errors[] = "Fila {$excelRow}: datos incompletos, omitida";
                continue;
            }

            try {
                // 1. Buscar o crear cliente
                $client = Client::where('document_number', $documento)->first();
                if (!$client) {
                    $client = Client::create([
                        'first_name' => $nombre,
                        'last_name' => $apellido,
                        'document_type' => trim($row[self::COL_TIPO_DOC] ?? '') ?: 'CC',
                        'document_number' => $documento,
                        'phone' => trim($row[self::COL_TELEFONO] ?? '') ?: null,
                        'email' => trim($row[self::COL_EMAIL] ?? '') ?: null,
                        'address' => trim($row[self::COL_DIRECCION] ?? '') ?: null,
                        'whatsapp_authorized' => true,
                    ]);
                    $clientsCreated++;
                }

                // 2. Crear fórmula si hay datos
                $formulaId = null;
                if ($this->hasFormulaData($row)) {
                    $formula = Formula::create([
                        'client_id' => $client->id,
                        'od_sphere' => $this->parseNumber($row[self::COL_OD_ESFERA] ?? ''),
                        'od_cylinder' => $this->parseNumber($row[self::COL_OD_CILINDRO] ?? ''),
                        'od_axis' => $this->parseNumber($row[self::COL_OD_EJE] ?? ''),
                        'od_add' => $this->parseNumber($row[self::COL_OD_ADD] ?? ''),
                        'oi_sphere' => $this->parseNumber($row[self::COL_OI_ESFERA] ?? ''),
                        'oi_cylinder' => $this->parseNumber($row[self::COL_OI_CILINDRO] ?? ''),
                        'oi_axis' => $this->parseNumber($row[self::COL_OI_EJE] ?? ''),
                        'oi_add' => $this->parseNumber($row[self::COL_OI_ADD] ?? ''),
                        'exam_date' => now(),
                    ]);
                    $formulaId = $formula->id;
                }

                // 3. Determinar laboratorio
                $labName = trim($row[self::COL_LABORATORIO] ?? '');
                $lab = Laboratory::where('name', 'like', "%{$labName}%")->first()
                    ?? Laboratory::where('is_active', true)->first();

                // 4. Parsear montura
                $monturaRaw = trim($row[self::COL_MONTURA] ?? '');
                $frameType = str_contains(mb_strtolower($monturaRaw), 'propia') ? 'own' : 'purchased';
                $frameBrand = '';
                if (str_contains($monturaRaw, ' - ')) {
                    $frameBrand = trim(explode(' - ', $monturaRaw, 2)[1] ?? '');
                }

                // 5. Crear trabajo
                $work = Work::create([
                    'tracking_code' => Work::generateTrackingCode(),
                    'client_id' => $client->id,
                    'laboratory_id' => $lab->id,
                    'formula_id' => $formulaId,
                    'user_id' => Auth::id(),
                    'status' => 'registered',
                    'frame_type' => $frameType,
                    'frame_brand' => $frameBrand ?: null,
                    'lens_type' => $this->parseLensType($tipoLente),
                    'lens_material' => $this->parseLensMaterial(trim($row[self::COL_MATERIAL] ?? '')),
                    'treatment_antireflective' => $this->parseBool($row[self::COL_ANTIRREFLEJO] ?? ''),
                    'treatment_photochromic' => $this->parseBool($row[self::COL_FOTOCROMATICO] ?? ''),
                    'treatment_blue_filter' => $this->parseBool($row[self::COL_FILTRO_AZUL] ?? ''),
                    'treatment_polarized' => $this->parseBool($row[self::COL_POLARIZADO] ?? ''),
                    'price_lenses' => $this->parseNumber($row[self::COL_PRECIO_LENTES] ?? '') ?: 0,
                    'price_frame' => $this->parseNumber($row[self::COL_PRECIO_MONTURA] ?? '') ?: 0,
                    'price_total' => $precioTotal,
                    'observations' => trim($row[self::COL_OBSERVACIONES] ?? '') ?: null,
                ]);

                // 6. Registrar estado inicial
                WorkStatusChange::create([
                    'work_id' => $work->id,
                    'user_id' => Auth::id(),
                    'from_status' => null,
                    'to_status' => 'registered',
                    'notes' => 'Importado desde Excel',
                ]);

                // 7. Registrar abono si hay
                $abono = $this->parseNumber($row[self::COL_ABONO] ?? '');
                if ($abono > 0) {
                    Payment::create([
                        'work_id' => $work->id,
                        'user_id' => Auth::id(),
                        'amount' => $abono,
                        'method' => 'cash',
                        'notes' => 'Abono registrado desde Excel',
                    ]);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Fila {$excelRow}: error al importar - " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'clients_created' => $clientsCreated,
            'errors' => $errors,
        ];
    }

    /**
     * Obtener solo las filas de datos (saltando título, instrucción, categorías, headers).
     */
    protected function getDataRows(): array
    {
        // Filas de datos empiezan en índice 4 (fila 5 de Excel)
        // Filtrar la fila de TOTALES al final
        $dataRows = array_slice($this->rows, 4);

        return array_filter($dataRows, function ($row) {
            // Ignorar fila de totales
            $lastCol = trim($row[self::COL_OBSERVACIONES] ?? '');
            return $lastCol !== 'TOTALES';
        });
    }

    protected function parseNumber($val): float
    {
        if (is_numeric($val)) return (float) $val;
        $val = str_replace(['$', '.', ' '], '', $val);
        $val = str_replace(',', '.', $val);
        return is_numeric($val) ? (float) $val : 0;
    }

    protected function parseBool($val): bool
    {
        return in_array(mb_strtolower(trim($val)), ['sí', 'si', 'yes', '1', 'true']);
    }

    protected function parseLensType(string $val): string
    {
        $val = mb_strtolower(trim($val));
        if (str_contains($val, 'bifocal')) return 'bifocal';
        if (str_contains($val, 'progresivo')) return 'progressive';
        return 'monofocal';
    }

    protected function parseLensMaterial(string $val): string
    {
        $val = mb_strtolower(trim($val));
        if (str_contains($val, 'policarbonato') || str_contains($val, 'polycarb')) return 'polycarbonate';
        if (str_contains($val, 'alto') || str_contains($val, 'high')) return 'high_index';
        if (str_contains($val, 'trivex')) return 'trivex';
        return 'cr39';
    }

    protected function hasFormulaData(array $row): bool
    {
        for ($i = self::COL_OD_ESFERA; $i <= self::COL_OI_ADD; $i++) {
            if (!empty($row[$i]) && is_numeric($row[$i])) return true;
        }
        return false;
    }
}
