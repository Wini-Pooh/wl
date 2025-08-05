<?php

use Illuminate\Support\Facades\Route;

// Маршруты для партнеров
// Доступ к сметам: partner, employee, foreman, estimator, admin
// Доступ к проектам: partner, employee, foreman, admin (НЕ estimator)  
Route::prefix('partner')->name('partner.')->group(function () {
    // Общий дашборд (доступен всем ролям партнера)
    Route::middleware(['auth', 'role:partner,employee,foreman,estimator,admin'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Partner\AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/analytics/chart-data', [App\Http\Controllers\Partner\AnalyticsController::class, 'getChartData'])->name('analytics.chart-data');
    });
    
    // Маршруты для работы со сметами (доступны всем: partner, employee, foreman, estimator, admin)
    // Прорабы и сметчики имеют полный доступ к сметам
    Route::middleware(['auth', 'role:partner,employee,foreman,estimator,admin'])->group(function () {
        Route::resource('estimates', App\Http\Controllers\Partner\EstimateController::class);
        
        // Автосохранение смет
        Route::post('estimates/{estimate}/autosave', [App\Http\Controllers\Partner\EstimateController::class, 'autosave'])->name('estimates.autosave');
        
        // Сохранение шаблонов смет (проверяем лимит шаблонов)
        Route::post('estimates/{estimate}/save-template', [App\Http\Controllers\Partner\EstimateController::class, 'saveAsTemplate'])
            ->middleware('subscription.limits:estimate_templates')
            ->name('estimates.save-template');
        
        // Получение шаблонов по типу
        Route::get('estimates/templates/{type}', [App\Http\Controllers\Partner\EstimateController::class, 'getTemplatesByType'])->name('estimates.templates');
        
        // Экспорт смет
        Route::post('estimates/{estimate}/export-pdf', [App\Http\Controllers\Partner\EstimateController::class, 'exportPdf'])->name('estimates.export-pdf');
        Route::post('estimates/{estimate}/export-excel', [App\Http\Controllers\Partner\EstimateController::class, 'exportExcel'])->name('estimates.export-excel');
    });
    
    // Маршруты для работы с проектами/объектами (НЕ доступны сметчикам)
    // Создание и редактирование объектов только для партнеров, сотрудников и админов (НЕ прорабов)
    Route::middleware(['auth', 'role:partner,employee,admin'])->group(function () {
        Route::get('projects/create', [App\Http\Controllers\Partner\ProjectController::class, 'create'])
            ->middleware('subscription.limits:active_projects')
            ->name('projects.create');
        Route::post('projects', [App\Http\Controllers\Partner\ProjectController::class, 'store'])
            ->middleware('subscription.limits:active_projects')
            ->name('projects.store');
        Route::get('projects/{project}/edit', [App\Http\Controllers\Partner\ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [App\Http\Controllers\Partner\ProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/{project}', [App\Http\Controllers\Partner\ProjectController::class, 'destroy'])->name('projects.destroy');
        
        // Управление сотрудниками в проектах
        Route::get('projects/assignable-employees', [App\Http\Controllers\Partner\ProjectController::class, 'getAllAssignableEmployees'])->name('projects.all-assignable-employees');
        Route::post('projects/{project}/assign-employees', [App\Http\Controllers\Partner\ProjectController::class, 'assignEmployees'])->name('projects.assign-employees');
        Route::get('projects/{project}/assignable-employees', [App\Http\Controllers\Partner\ProjectController::class, 'getAssignableEmployees'])->name('projects.assignable-employees');
        Route::delete('projects/{project}/employees/{employee}', [App\Http\Controllers\Partner\ProjectController::class, 'removeEmployee'])->name('projects.remove-employee');
        
        // Удалены маршруты для системы шаблонов документов
    });
    
    // Просмотр проектов доступен партнерам, сотрудникам, прорабам, клиентам и админам (НЕ сметчикам)
    Route::middleware(['auth', 'role:partner,employee,foreman,client,admin'])->group(function () {
        Route::get('projects', [App\Http\Controllers\Partner\ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}', [App\Http\Controllers\Partner\ProjectController::class, 'show'])->name('projects.show');
        
        // Отдельные страницы для каждой вкладки проекта (используем page- префикс, чтобы избежать конфликтов с API маршрутами)
        Route::get('projects/{project}/page/main', [App\Http\Controllers\Partner\ProjectController::class, 'showMain'])->name('projects.main');
        Route::get('projects/{project}/page/finance', [App\Http\Controllers\Partner\ProjectController::class, 'showFinance'])->name('projects.finance');
        Route::get('projects/{project}/page/schedule', [App\Http\Controllers\Partner\ProjectController::class, 'showSchedule'])->name('projects.schedule');
        Route::get('projects/{project}/page/photos', [App\Http\Controllers\Partner\ProjectController::class, 'showPhotos'])->name('projects.photos');
        Route::get('projects/{project}/page/design', [App\Http\Controllers\Partner\ProjectController::class, 'showDesign'])->name('projects.design');
        Route::get('projects/{project}/page/schemes', [App\Http\Controllers\Partner\ProjectController::class, 'showSchemes'])->name('projects.schemes');
        Route::get('projects/{project}/page/documents', [App\Http\Controllers\Partner\ProjectController::class, 'showDocuments'])->name('projects.documents');
        
        // Действия со схемами проекта
        Route::post('projects/{project}/schemes/upload', [App\Http\Controllers\Partner\ProjectController::class, 'uploadSchemes'])->name('projects.schemes.upload');
        Route::delete('projects/{project}/schemes/{schemeId}', [App\Http\Controllers\Partner\ProjectController::class, 'deleteScheme'])->name('projects.schemes.delete');
        Route::get('projects/{project}/schemes/{schemeId}/download', [App\Http\Controllers\Partner\ProjectController::class, 'downloadScheme'])->name('projects.schemes.download');
        
        // Действия с документами проекта
        Route::post('projects/{project}/documents/upload', [App\Http\Controllers\Partner\ProjectController::class, 'uploadDocuments'])->name('projects.documents.upload');
        Route::delete('projects/{project}/documents/{documentId}', [App\Http\Controllers\Partner\ProjectController::class, 'deleteDocument'])->name('projects.documents.delete');
        Route::get('projects/{project}/documents/{documentId}/download', [App\Http\Controllers\Partner\ProjectController::class, 'downloadDocument'])->name('projects.documents.download');
        
        Route::post('projects/search-by-phone', [App\Http\Controllers\Partner\ProjectController::class, 'searchByPhone'])->name('projects.search-by-phone');
        
        // Тестовая страница для проверки навигации проекта
        Route::get('/test/project-navigation', function () {
            return view('test.project-navigation');
        })->name('test.project-navigation');
        
        // Тестовая страница для проверки кнопок
        Route::get('projects/{project}/test-buttons', function (\App\Models\Project $project) {
            return view('partner.projects.test-buttons', compact('project'));
        })->name('projects.test-buttons');
        
        // Тестовая страница для финансов
        Route::get('projects/{project}/test-finance', function (\App\Models\Project $project) {
            return view('partner.projects.test-finance', compact('project'));
        })->name('projects.test-finance');
        
        // Тестовая страница для проверки автообновления финансовых показателей  
        Route::get('/test/finance-automation', function () {
            return view('test.finance-automation');
        })->name('test.finance-automation');
        
        // Отладочная страница для проверки ролей
        Route::get('/debug/role-check', function () {
            return view('debug.role-check');
        })->name('debug.role-check');
    });
    
    // Маршруты для функционала внутри проектов - НЕ доступны сметчикам (только партнерам, сотрудникам, прорабам, клиентам и админам)
    // Прорабы имеют полный доступ ко всему функционалу на странице show проекта
    // Клиенты имеют доступ только на чтение
    Route::middleware(['auth', 'role:partner,employee,foreman,client,admin'])->group(function () {
        // Маршруты для финансов проектов
        Route::prefix('projects/{project}')->name('projects.')->group(function () {
        // Работы
        Route::post('works', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'storeWork'])->name('works.store');
        Route::get('works/{work}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'showWork'])->name('works.show');
        Route::put('works/{work}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'updateWork'])->name('works.update');
        Route::delete('works/{work}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'destroyWork'])->name('works.destroy');
        
        // Материалы
        Route::post('materials', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'storeMaterial'])->name('materials.store');
        Route::get('materials/{material}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'showMaterial'])->name('materials.show');
        Route::put('materials/{material}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'updateMaterial'])->name('materials.update');
        Route::delete('materials/{material}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'destroyMaterial'])->name('materials.destroy');
        
        // Транспортировка
        Route::post('transports', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'storeTransport'])->name('transports.store');
        Route::get('transports/{transport}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'showTransport'])->name('transports.show');
        Route::put('transports/{transport}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'updateTransport'])->name('transports.update');
        Route::delete('transports/{transport}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'destroyTransport'])->name('transports.destroy');
        
        // Этапы проекта
        Route::post('stages', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'storeStage'])->name('stages.store');
        Route::get('stages/{stage}', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'showStage'])->name('stages.show');
        Route::put('stages/{stage}', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'updateStage'])->name('stages.update');
        Route::delete('stages/{stage}', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'destroyStage'])->name('stages.destroy');
        Route::patch('stages/{stage}/complete', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'completeStage'])->name('stages.complete');
        
        // События проекта
        Route::post('events', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'storeEvent'])->name('events.store');
        Route::get('events/{event}', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'showEvent'])->name('events.show');
        Route::put('events/{event}', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'updateEvent'])->name('events.update');
        Route::delete('events/{event}', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'destroyEvent'])->name('events.destroy');
        Route::patch('events/{event}/complete', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'completeEvent'])->name('events.complete');
        
        // Фотографии проекта
        Route::get('photos', [App\Http\Controllers\Partner\ProjectPhotoController::class, 'index'])->name('photos.index');
        Route::post('photos', [App\Http\Controllers\Partner\ProjectPhotoController::class, 'store'])->name('photos.store');
        Route::post('photos/upload', [App\Http\Controllers\Partner\ProjectPhotoController::class, 'upload'])->name('photos.upload');
        Route::post('photos/{fileId}/delete', [App\Http\Controllers\Partner\ProjectPhotoController::class, 'delete'])->name('photos.delete');
        Route::get('photos/{fileId}', [App\Http\Controllers\Partner\ProjectPhotoController::class, 'show'])->name('photos.show');
        Route::get('photos/{fileId}/download', [App\Http\Controllers\Partner\ProjectPhotoController::class, 'download'])->name('photos.download');
        Route::delete('photos/{fileId}', [App\Http\Controllers\Partner\ProjectPhotoController::class, 'destroy'])->name('photos.destroy');
        Route::post('photos/migrate', [App\Http\Controllers\Partner\ProjectPhotoController::class, 'migrateExistingPhotos'])->name('photos.migrate');
        
        // Дизайн проекта
        Route::get('design', [App\Http\Controllers\Partner\ProjectDesignController::class, 'index'])->name('design.index');
        Route::post('design', [App\Http\Controllers\Partner\ProjectDesignController::class, 'store'])->name('design.store');
        Route::get('design/{fileId}', [App\Http\Controllers\Partner\ProjectDesignController::class, 'show'])->name('design.show');
        Route::get('design/{fileId}/view', [App\Http\Controllers\Partner\ProjectController::class, 'showDesignFile'])->name('design.view');
        Route::get('design/{fileId}/download', [App\Http\Controllers\Partner\ProjectDesignController::class, 'download'])->name('design.download');
        Route::delete('design/{fileId}', [App\Http\Controllers\Partner\ProjectDesignController::class, 'destroy'])->name('design.destroy');
        
        // Схемы проекта
        Route::get('schemes', [App\Http\Controllers\Partner\ProjectSchemeController::class, 'index'])->name('schemes.index');
        Route::post('schemes', [App\Http\Controllers\Partner\ProjectSchemeController::class, 'store'])->name('schemes.store');
        Route::get('schemes/{fileId}', [App\Http\Controllers\Partner\ProjectSchemeController::class, 'show'])->name('schemes.show');
        Route::get('schemes/{fileId}/download', [App\Http\Controllers\Partner\ProjectSchemeController::class, 'download'])->name('schemes.download');
        Route::delete('schemes/{fileId}', [App\Http\Controllers\Partner\ProjectSchemeController::class, 'destroy'])->name('schemes.destroy');
        
        // Документы проекта
        Route::get('documents', [App\Http\Controllers\Partner\ProjectDocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/types', [App\Http\Controllers\Partner\ProjectDocumentController::class, 'getDocumentTypes'])->name('documents.types');
        Route::get('documents/upload-modal', [App\Http\Controllers\Partner\ProjectDocumentController::class, 'uploadModal'])->name('documents.upload-modal');
        Route::post('documents', [App\Http\Controllers\Partner\ProjectDocumentController::class, 'store'])->name('documents.store');
        Route::get('documents/{fileId}/download', [App\Http\Controllers\Partner\ProjectDocumentController::class, 'download'])->name('documents.download');
        Route::get('documents/{fileId}', [App\Http\Controllers\Partner\ProjectDocumentController::class, 'show'])->name('documents.show');
        Route::delete('documents/{fileId}', [App\Http\Controllers\Partner\ProjectDocumentController::class, 'destroy'])->name('documents.destroy');
        
        // Финансовые данные - основные методы
        Route::get('finance', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'index'])->name('finance.index');
        Route::get('finance/summary', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getFinanceSummary'])->name('finance.summary');
        Route::get('finance/counts', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getFinanceCounts'])->name('finance.counts');
        Route::post('finance/generate-pdf', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'generateFinancePDF'])->name('finance.generate-pdf');
        Route::get('finance-pdf', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'downloadFinancePDF'])->name('finance-pdf');
        Route::get('finance/download-pdf', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'downloadFinancePDF'])->name('finance.download-pdf');
        
        // Финансовые записи проекта - CRUD операции
        Route::post('finances', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'storeFinance'])->name('finances.store');
        Route::get('finances/{finance}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'showFinance'])->name('finances.show');
        Route::put('finances/{finance}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'updateFinance'])->name('finances.update');
        Route::delete('finances/{finance}', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'destroyFinance'])->name('finances.destroy');
        
        // AJAX маршруты для частичной загрузки данных - ИСПРАВЛЕННЫЕ
        Route::get('finance/works-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getWorksPartial'])->name('finance.works.partial');
        Route::get('finance/materials-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getMaterialsPartial'])->name('finance.materials.partial');
        Route::get('finance/transports-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getTransportsPartial'])->name('finance.transports.partial');
        
        // CRUD операции для работ, материалов и транспорта - ИСПРАВЛЕННЫЕ
        Route::post('works', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'storeWork'])->name('works.store');
        Route::post('materials', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'storeMaterial'])->name('materials.store'); 
        Route::post('transports', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'storeTransport'])->name('transports.store');
        
        // Данные расписания
        Route::get('schedule', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'index'])->name('schedule.index');
        Route::get('schedule/summary', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'summary'])->name('schedule.summary');
        Route::get('schedule/counts', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'getCounts'])->name('schedule.counts');
        
        // Частичная загрузка этапов и событий для AJAX
        Route::get('stages-partial', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'getStagesPartial'])->name('stages.partial');
        Route::get('events-partial', [App\Http\Controllers\Partner\ProjectScheduleController::class, 'getEventsPartial'])->name('events.partial');
        
        // Модальные окна
        Route::get('modals/{type}', [App\Http\Controllers\ProjectModalController::class, 'getModal'])->name('modals.show');
    });
    });
    
    // Маршруты для управления сотрудниками (только для партнеров, сотрудников и админов - НЕ прорабов и НЕ сметчиков)
    Route::middleware(['auth', 'role:partner,employee,admin'])->group(function () {
        Route::get('employees/dashboard', [App\Http\Controllers\Partner\EmployeeController::class, 'dashboard'])->name('employees.dashboard');
        Route::get('employees/api', [App\Http\Controllers\Partner\EmployeeController::class, 'apiEmployees'])->name('employees.api');
        Route::resource('employees', App\Http\Controllers\Partner\EmployeeController::class);
        Route::post('employees/search-by-phone', [App\Http\Controllers\Partner\EmployeeController::class, 'searchByPhone'])->name('employees.search-by-phone');
        Route::get('employees/search-users-by-phone', [App\Http\Controllers\Partner\EmployeeController::class, 'searchUsersByPhone'])->name('employees.searchUsersByPhone');
        
        // Финансовые операции теперь интегрированы в основную страницу сотрудника
        Route::prefix('employees/{employee}')->name('employees.')->group(function () {
            Route::post('finances', [App\Http\Controllers\Partner\EmployeeFinanceController::class, 'store'])->name('finances.store');
            Route::get('finances/{finance}', [App\Http\Controllers\Partner\EmployeeFinanceController::class, 'show'])->name('finances.show');
            Route::put('finances/{finance}', [App\Http\Controllers\Partner\EmployeeFinanceController::class, 'update'])->name('finances.update');
            Route::post('finances/{finance}/mark-paid', [App\Http\Controllers\Partner\EmployeeFinanceController::class, 'markAsPaid'])->name('finances.mark-paid');
            Route::delete('finances/{finance}', [App\Http\Controllers\Partner\EmployeeFinanceController::class, 'destroy'])->name('finances.destroy');
            Route::post('check-overdue', [App\Http\Controllers\Partner\EmployeeFinanceController::class, 'checkOverdue'])->name('check-overdue');
        });
    });
    
    // Альтернативные маршруты для финансов без привязки модели - ВРЕМЕННОЕ РЕШЕНИЕ
    Route::middleware(['auth', 'role:partner,employee,foreman,client,admin'])->group(function () {
        Route::prefix('finance-api')->name('finance.api.')->group(function () {
            Route::get('projects/{projectId}/summary', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getFinanceSummary'])->name('summary');
            Route::get('projects/{projectId}/counts', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getFinanceCounts'])->name('counts');
            Route::get('projects/{projectId}/works-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getWorksPartial'])->name('works.partial');
            Route::get('projects/{projectId}/materials-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getMaterialsPartial'])->name('materials.partial');
            Route::get('projects/{projectId}/transports-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getTransportsPartial'])->name('transports.partial');
        });
    });
});
