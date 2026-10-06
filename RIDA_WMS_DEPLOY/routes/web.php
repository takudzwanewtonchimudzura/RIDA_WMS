<?php
// routes/web.php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BoreholeController;
use App\Http\Controllers\BoreholeMapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BoreholeReportController;
use App\Http\Controllers\BoreholeExportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DamController;
use App\Http\Controllers\DamMaintenanceController;
use App\Http\Controllers\IrrigationSchemeController;
use App\Http\Controllers\IrrigationMaintenanceController;
use App\Http\Controllers\PipedWaterSchemeController;
use App\Http\Controllers\SchemeMaintenanceController;
use App\Http\Controllers\ProspectingController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\WaterPointController;
use App\Http\Controllers\WaterQualityTestController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UsageReportController;
use App\Http\Controllers\ConsumableCategoryController;

// Authentication Routes
Auth::routes(['register' => false]);

// Protected Routes
Route::middleware(['auth'])->group(function () {

    // Keeps the session alive while the user is actively using the app.
    Route::post('/session/heartbeat', fn () => response()->noContent())->name('session.heartbeat');

    // Client accounts and invoicing are restricted to administrators.
    Route::resource('clients', ClientController::class)->except(['destroy']);
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payments.store');
    Route::patch('/invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
    Route::patch('/invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');

    // Home / Dashboard
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/chart-data', [DashboardController::class, 'getChartData']);

    // Prospecting and ticket workflows
    Route::resource('prospecting', ProspectingController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->parameters(['prospecting' => 'prospectingSite']);
    Route::patch('/prospecting/{prospectingSite}/decision', [ProspectingController::class, 'decide'])->name('prospecting.decide');
    Route::patch('/prospecting/{prospectingSite}/mark-drilled', [ProspectingController::class, 'markDrilled'])->name('prospecting.mark-drilled');
    Route::resource('tickets', SupportTicketController::class)->only(['index', 'create', 'store', 'show', 'update']);

    // -------------------------------------------------------
    // Borehole Routes
    // -------------------------------------------------------
    Route::get('/boreholes', [BoreholeController::class, 'index'])->name('boreholes.index');
    Route::get('/boreholes/create', [BoreholeController::class, 'create'])->name('boreholes.create');
    Route::post('/boreholes', [BoreholeController::class, 'store'])->name('boreholes.store');
    Route::get('/boreholes/{id}/edit', [BoreholeController::class, 'edit'])->name('boreholes.edit');
    Route::put('/boreholes/{id}', [BoreholeController::class, 'update'])->name('boreholes.update');
    Route::delete('/boreholes/{id}', [BoreholeController::class, 'destroy'])->name('boreholes.destroy');
    Route::patch('/boreholes/{borehole}/approve', [BoreholeController::class, 'approve'])->name('boreholes.approve');
    Route::patch('/boreholes/{borehole}/unapprove', [BoreholeController::class, 'unapprove'])->name('boreholes.unapprove');
    Route::get('/boreholes/{borehole}', [BoreholeController::class, 'show'])->name('boreholes.show');

    // PDF & Excel Export
    Route::get('/boreholes/{borehole}/pdf', [BoreholeReportController::class, 'generatePDF'])->name('boreholes.pdf.download');
    Route::get('/boreholes/{borehole}/certificate', [BoreholeReportController::class, 'generateCertificate'])->name('boreholes.certificate.download');
    Route::get('/boreholes/export/excel', [BoreholeExportController::class, 'exportExcel'])->name('boreholes.export.excel');

    // Map
    Route::get('/maps/geographic', [BoreholeMapController::class, 'geographicMaps'])->name('maps.geographic');
    Route::get('/api/boreholes/map-data', [BoreholeMapController::class, 'getBoreholeData'])->name('api.boreholes.map-data');

    // -------------------------------------------------------
    // Dam Routes
    // -------------------------------------------------------
    Route::get('/dams', [DamController::class, 'index'])->name('dams.index');
    Route::get('/dams/create', [DamController::class, 'create'])->name('dams.create');
    Route::post('/dams', [DamController::class, 'store'])->name('dams.store');
    Route::patch('/dams/{dam}/approve', [DamController::class, 'approve'])->name('dams.approve');
    Route::patch('/dams/{dam}/unapprove', [DamController::class, 'unapprove'])->name('dams.unapprove');
    Route::get('/dams/levels', [DamController::class, 'levels'])->name('dams.levels');
    Route::get('/dams/inspections', [DamController::class, 'inspections'])->name('dams.inspections');
    Route::get('/dams/export', [DamController::class, 'export'])->name('dams.export');
    Route::get('/dams/search', [DamController::class, 'search'])->name('dams.search');
    Route::get('/dams/{id}/edit', [DamController::class, 'edit'])->name('dams.edit');
    Route::put('/dams/{dam}', [DamController::class, 'update'])->name('dams.update');
    Route::delete('/dams/{id}', [DamController::class, 'destroy'])->name('dams.destroy');
    Route::get('/dams/{id}', [DamController::class, 'show'])->name('dams.show');
    Route::resource('dams.maintenance', DamMaintenanceController::class);

    // -------------------------------------------------------
    // Piped Water Schemes Routes
    // -------------------------------------------------------
    Route::get('/piped-water-schemes', [PipedWaterSchemeController::class, 'index'])->name('piped-water-schemes.index');
    Route::get('/piped-water-schemes/create', [PipedWaterSchemeController::class, 'create'])->name('piped-water-schemes.create');
    Route::post('/piped-water-schemes', [PipedWaterSchemeController::class, 'store'])->name('piped-water-schemes.store');
    Route::get('/piped-water-schemes/networks', [PipedWaterSchemeController::class, 'networks'])->name('piped-water-schemes.networks');
    Route::get('/piped-water-schemes/quality', [PipedWaterSchemeController::class, 'quality'])->name('piped-water-schemes.quality');
    Route::post('/piped-water-schemes/{pipedWaterScheme}/quality-tests', [WaterQualityTestController::class, 'store'])->name('piped-water-schemes.quality-tests.store');
    Route::get('/piped-water-schemes/{pipedWaterScheme}/quality-tests', [WaterQualityTestController::class, 'history'])->name('piped-water-schemes.quality-tests.history');
    Route::get('/piped-water-schemes/{pipedWaterScheme}/edit', [PipedWaterSchemeController::class, 'edit'])->name('piped-water-schemes.edit');
    Route::put('/piped-water-schemes/{pipedWaterScheme}', [PipedWaterSchemeController::class, 'update'])->name('piped-water-schemes.update');
    Route::delete('/piped-water-schemes/{pipedWaterScheme}', [PipedWaterSchemeController::class, 'destroy'])->name('piped-water-schemes.destroy');
    Route::get('/piped-water-schemes/{pipedWaterScheme}', [PipedWaterSchemeController::class, 'show'])->name('piped-water-schemes.show');

    // -------------------------------------------------------
    // Irrigation Scheme Routes
    // -------------------------------------------------------
    Route::resource('irrigation-schemes', IrrigationSchemeController::class);
    Route::resource('irrigation-maintenances', IrrigationMaintenanceController::class);
        Route::resource('scheme-maintenances', SchemeMaintenanceController::class);
    Route::resource('water-points', WaterPointController::class);

    Route::prefix('consumables')->name('consumables.')->group(function () {
        Route::get('/usage-reports/summary', [UsageReportController::class, 'generateReport'])->name('usage-reports.report');
        Route::get('/usage-reports/export', [UsageReportController::class, 'export'])->name('usage-reports.export');
        Route::post('/inventory/{consumable}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
        Route::post('/stock-requests/{stockRequest}/approve', [StockRequestController::class, 'approve'])->name('stock-requests.approve');
        Route::post('/stock-requests/{stockRequest}/issue', [StockRequestController::class, 'issue'])->name('stock-requests.issue');
        Route::post('/stock-requests/{stockRequest}/reject', [StockRequestController::class, 'reject'])->name('stock-requests.reject');
        Route::resource('inventory', InventoryController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
            ->parameters(['inventory' => 'consumable']);
        Route::resource('categories', ConsumableCategoryController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('stock-requests', StockRequestController::class)->only(['index', 'create', 'store', 'show']);
        Route::resource('usage-reports', UsageReportController::class)->only(['index', 'create', 'store', 'show']);
    });

    // -------------------------------------------------------
    // User Management Routes (admin check done inside controller)
    // -------------------------------------------------------
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/roles', [UserController::class, 'roles'])->name('users.roles');
    Route::get('/users/activity', [UserController::class, 'activity'])->name('users.activity');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    // -------------------------------------------------------
    // Reports Routes
    // -------------------------------------------------------
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/executive-dashboard', [ReportController::class, 'executiveDashboard'])->name('executive.dashboard');
        Route::get('/operational-performance', [ReportController::class, 'operationalPerformance'])->name('operational.performance');
        Route::get('/technical-analysis', [ReportController::class, 'technicalAnalysis'])->name('technical.analysis');
        Route::get('/consumables-performance', [ReportController::class, 'consumablesPerformance'])->name('consumables.performance');
        Route::get('/geographic-distribution', [ReportController::class, 'geographicDistribution'])->name('geographic.distribution');
        Route::get('/financial', [ReportController::class, 'financialReports'])->name('financial');

        Route::get('/export/executive/{format}', [ReportController::class, 'exportExecutive'])->name('export.executive');
        Route::get('/export/operational/{format}', [ReportController::class, 'exportOperational'])->name('export.operational');
        Route::get('/export/technical/{format}', [ReportController::class, 'exportTechnical'])->name('export.technical');
        Route::get('/export/geographic/{format}', [ReportController::class, 'exportGeographic'])->name('export.geographic');

        Route::get('/api/executive-data', [ReportController::class, 'getExecutiveData'])->name('api.executive.data');
        Route::get('/api/operational-data', [ReportController::class, 'getOperationalData'])->name('api.operational.data');
    });

    // Analytics
    Route::get('/analytics/dashboard', [ReportController::class, 'analyticsDashboard'])->name('analytics.dashboard');
});