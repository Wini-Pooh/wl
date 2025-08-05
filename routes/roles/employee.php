<?php

use Illuminate\Support\Facades\Route;

// Маршруты для сотрудников 
// Дашборд доступен для сотрудников, прорабов, сметчиков и админов
Route::middleware(['auth', 'employee:employee,foreman,estimator,admin'])->prefix('employee')->name('employee.')->group(function () {
    
    // Главный дашборд для сотрудников/прорабов/сметчиков
    Route::get('/dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');
});

// Маршруты только для сотрудников и админов (НЕ для прорабов и сметчиков)
// Прорабы и сметчики используют партнерские маршруты
Route::middleware(['auth', 'employee:employee,admin'])->prefix('employee')->name('employee.')->group(function () {
    
    // Маршруты для работы с проектами (доступны только сотрудникам и админам)
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employee\ProjectController::class, 'index'])->name('index');
        Route::get('/{project}', [App\Http\Controllers\Employee\ProjectController::class, 'show'])->name('show');
        Route::post('/search-by-phone', [App\Http\Controllers\Employee\ProjectController::class, 'searchByPhone'])->name('search-by-phone');
        
        // Создание и редактирование проектов
        Route::get('/create', [App\Http\Controllers\Employee\ProjectController::class, 'create'])
            ->middleware('subscription.limits:active_projects')
            ->name('create');
        Route::post('/', [App\Http\Controllers\Employee\ProjectController::class, 'store'])
            ->middleware('subscription.limits:active_projects')
            ->name('store');
        Route::get('/{project}/edit', [App\Http\Controllers\Employee\ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}', [App\Http\Controllers\Employee\ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [App\Http\Controllers\Employee\ProjectController::class, 'destroy'])->name('destroy');
        
        // Финансовые данные проектов
        Route::prefix('{project}')->group(function () {
            // Работы
            Route::post('works', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'storeWork'])->name('works.store');
            Route::get('works/{work}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'showWork'])->name('works.show');
            Route::put('works/{work}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'updateWork'])->name('works.update');
            Route::delete('works/{work}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'destroyWork'])->name('works.destroy');
            
            // Материалы
            Route::post('materials', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'storeMaterial'])->name('materials.store');
            Route::get('materials/{material}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'showMaterial'])->name('materials.show');
            Route::put('materials/{material}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'updateMaterial'])->name('materials.update');
            Route::delete('materials/{material}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'destroyMaterial'])->name('materials.destroy');
            
            // Транспорт
            Route::post('transports', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'storeTransport'])->name('transports.store');
            Route::get('transports/{transport}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'showTransport'])->name('transports.show');
            Route::put('transports/{transport}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'updateTransport'])->name('transports.update');
            Route::delete('transports/{transport}', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'destroyTransport'])->name('transports.destroy');
            
            // Финансовая сводка
            Route::get('finance/summary', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'getFinanceSummary'])->name('finance.summary');
            
            // Частичные данные для AJAX
            Route::get('finance/works-partial', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'getWorksPartial'])->name('finance.works.partial');
            Route::get('finance/materials-partial', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'getMaterialsPartial'])->name('finance.materials.partial');
            Route::get('finance/transports-partial', [App\Http\Controllers\Employee\ProjectFinanceController::class, 'getTransportsPartial'])->name('finance.transports.partial');
        });
    });
    
        // Маршруты для работы со сметами (доступны только сотрудникам и админам)
    Route::prefix('estimates')->name('estimates.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employee\EstimateController::class, 'index'])->name('index');
        Route::get('/{estimate}', [App\Http\Controllers\Employee\EstimateController::class, 'show'])->name('show');
        
        // Полный доступ к сметам для сотрудников и админов
        Route::get('/create', [App\Http\Controllers\Employee\EstimateController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Employee\EstimateController::class, 'store'])->name('store');
        Route::get('/{estimate}/edit', [App\Http\Controllers\Employee\EstimateController::class, 'edit'])->name('edit');
        Route::put('/{estimate}', [App\Http\Controllers\Employee\EstimateController::class, 'update'])->name('update');
        Route::delete('/{estimate}', [App\Http\Controllers\Employee\EstimateController::class, 'destroy'])->name('destroy');
    });
});

// ВАЖНО: Прорабы и сметчики используют партнерские маршруты!
// Они НЕ имеют доступа к employee маршрутам выше, а работают через:
// - partner.projects.* для проектов (только прорабы, сметчики НЕ имеют доступа к проектам)
// - partner.estimates.* для смет (и прорабы, и сметчики имеют полный доступ)
