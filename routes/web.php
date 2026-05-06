<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EmployeeController;

/**
 * =============================================
 * RUTAS DEL SISTEMA UNIVERSO VISUAL
 * =============================================
 * 
 * Estructura:
 * 1. Rutas públicas (login, tracking)
 * 2. Rutas protegidas (requieren login)
 *    - Acceso general (admin + seller)
 *    - Acceso solo admin (reportes)
 */

// =============================================
// RUTAS PÚBLICAS (sin login)
// =============================================
// PDF público del recibo (para compartir por WhatsApp)
Route::get('/recibo/{work}', [\App\Http\Controllers\PdfController::class, 'publicWork'])->name('pdf.work.public');

// Portal de seguimiento del cliente (página pública)
Route::get('/seguimiento/{code?}', [TrackingController::class, 'index'])->name('tracking');
Route::post('/seguimiento/buscar', [TrackingController::class, 'search'])->name('tracking.search');

// Chatbot público (clientes en el portal de seguimiento)
Route::post('/chat/public', [ChatController::class, 'publicChat'])->name('chat.public');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Redirección de la raíz al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =============================================
// RUTAS PROTEGIDAS (requieren login)
// =============================================
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Chatbot admin (acceso a datos del sistema)
    Route::post('/chat/admin', [ChatController::class, 'adminChat'])->name('chat.admin');

    // =============================================
    // DASHBOARD — Acceso: admin y seller
    // =============================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =============================================
    // TRABAJOS — Acceso: admin y seller
    // =============================================
    Route::get('/trabajos', [WorkController::class, 'index'])->name('works.index');
    Route::get('/trabajos/exportar', [WorkController::class, 'export'])->name('works.export')->middleware('role:admin');
    Route::post('/trabajos/importar/analizar', [WorkController::class, 'analyzeImport'])->name('works.analyzeImport')->middleware('role:admin');
    Route::post('/trabajos/importar', [WorkController::class, 'import'])->name('works.import')->middleware('role:admin');
    Route::get('/trabajos/crear', [WorkController::class, 'create'])->name('works.create');
    Route::post('/trabajos', [WorkController::class, 'store'])->name('works.store');
    Route::get('/trabajos/{work}', [WorkController::class, 'show'])->name('works.show');
    Route::get('/trabajos/{work}/editar', [WorkController::class, 'edit'])->name('works.edit');
    Route::put('/trabajos/{work}', [WorkController::class, 'update'])->name('works.update');
    Route::patch('/trabajos/{work}/estado', [WorkController::class, 'updateStatus'])->name('works.updateStatus');
    Route::post('/trabajos/{work}/pago', [WorkController::class, 'storePayment'])->name('works.storePayment');
    Route::delete('/trabajos/{work}/pago/{payment}', [WorkController::class, 'destroyPayment'])->name('works.destroyPayment');
    Route::post('/trabajos/{work}/enviar-recibo', [WorkController::class, 'sendReceipt'])->name('works.sendReceipt');

    // =============================================
    // CLIENTES — Acceso: admin y seller
    // =============================================
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clientes/exportar', [ClientController::class, 'export'])->name('clients.export')->middleware('role:admin');
    Route::get('/clientes/crear', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clientes', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clientes/cumpleanos', [ClientController::class, 'birthdays'])->name('clients.birthdays');
    Route::get('/clientes/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clientes/{client}/editar', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clientes/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::post('/clientes/{client}/felicitar', [ClientController::class, 'sendBirthdayGreeting'])->name('clients.sendBirthday');

    // =============================================
    // LABORATORIOS — Acceso: admin y seller
    // =============================================
    Route::get('/laboratorios', [LaboratoryController::class, 'index'])->name('laboratories.index');
    Route::post('/laboratorios', [LaboratoryController::class, 'store'])->name('laboratories.store');
    Route::get('/laboratorios/{laboratory}', [LaboratoryController::class, 'show'])->name('laboratories.show');
    Route::put('/laboratorios/{laboratory}', [LaboratoryController::class, 'update'])->name('laboratories.update');
    Route::post('/laboratorios/{laboratory}/pagar/{work}', [LaboratoryController::class, 'markWorkPaid'])->name('laboratories.markPaid');
    Route::delete('/laboratorios/{laboratory}/pagar/{work}', [LaboratoryController::class, 'unmarkWorkPaid'])->name('laboratories.unmarkPaid');

    // =============================================
    // PDFs — Acceso: admin y seller
    // =============================================
    Route::get('/pdf/trabajo/{work}', [\App\Http\Controllers\PdfController::class, 'work'])->name('pdf.work');
    Route::get('/pdf/cliente/{client}', [\App\Http\Controllers\PdfController::class, 'client'])->name('pdf.client');

    // =============================================
    // REPORTES Y TRABAJADORAS — Acceso: SOLO admin
    // =============================================
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reportes/exportar', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/resumen-diario', [ReportController::class, 'dailySummary'])->name('reports.daily');
        Route::get('/pdf/reportes', [\App\Http\Controllers\PdfController::class, 'report'])->name('pdf.report');

        // Trabajadoras (empleadas físicas — solo admin las gestiona)
        Route::get('/trabajadoras', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/trabajadoras', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/trabajadoras/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::put('/trabajadoras/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    });
});