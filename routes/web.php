<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Маршрут для демонстрации системы подписок
Route::get('/subscription-overview', function () {
    return view('subscription.overview');
})->name('subscription.overview');

// Тестовая страница для API сотрудников
Route::get('/test-employees-api', function () {
    return view('test-employees-api');
})->middleware('auth')->name('test.employees.api');

// Диагностика AJAX запросов
Route::get('/debug/ajax', function () {
    return view('debug.ajax-debug');
})->middleware('auth')->name('debug.ajax');

Auth::routes();

// Тестовый маршрут
Route::get('/test', [App\Http\Controllers\TestController::class, 'test'])->name('test');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Система документооборота
Route::middleware(['auth'])->group(function () {
    // Основные маршруты документов
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/edit', [App\Http\Controllers\DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'update'])->name('documents.update');
    // Route::delete('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy'); // УДАЛЕНИЕ ОТКЛЮЧЕНО
    
    // Отправка и подписание документов
    Route::post('/documents/{document}/send', [App\Http\Controllers\DocumentController::class, 'send'])->name('documents.send');
    Route::post('/documents/{document}/sign', [App\Http\Controllers\DocumentController::class, 'sign'])->name('documents.sign');
    Route::post('/documents/{document}/reject', [App\Http\Controllers\DocumentController::class, 'reject'])->name('documents.reject');
    Route::get('/documents/{document}/verify', [App\Http\Controllers\DocumentController::class, 'verify'])->name('documents.verify');
    Route::get('/documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/export-signature', [App\Http\Controllers\DocumentController::class, 'exportSignature'])->name('documents.export-signature');
    
    // AJAX маршруты для документов
    Route::get('/api/projects/{project}/data', [App\Http\Controllers\DocumentController::class, 'getProjectData'])->name('api.projects.data');
    Route::post('/api/documents/preview', [App\Http\Controllers\DocumentController::class, 'preview'])->name('api.documents.preview');
    
    // Тестовая страница для отладки подписи документов
    Route::get('/test-sign', function() {
        return view('test-sign');
    })->name('test.sign');
    
    // Маршруты для шаблонов документов
    Route::get('/document-templates', [App\Http\Controllers\DocumentTemplateController::class, 'index'])->name('document-templates.index');
    Route::get('/document-templates/create', [App\Http\Controllers\DocumentTemplateController::class, 'create'])->name('document-templates.create');
    Route::post('/document-templates', [App\Http\Controllers\DocumentTemplateController::class, 'store'])->name('document-templates.store');
    Route::get('/document-templates/{template}', [App\Http\Controllers\DocumentTemplateController::class, 'show'])->name('document-templates.show');
    Route::get('/document-templates/{template}/edit', [App\Http\Controllers\DocumentTemplateController::class, 'edit'])->name('document-templates.edit');
    Route::put('/document-templates/{template}', [App\Http\Controllers\DocumentTemplateController::class, 'update'])->name('document-templates.update');
    Route::delete('/document-templates/{template}', [App\Http\Controllers\DocumentTemplateController::class, 'destroy'])->name('document-templates.destroy');
    Route::get('/document-templates/{template}/get', [App\Http\Controllers\DocumentTemplateController::class, 'getTemplate'])->name('document-templates.get');
});

// Система подписок и тарифных планов
Route::middleware(['auth'])->group(function () {
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
        Route::get('/manage', [App\Http\Controllers\SubscriptionController::class, 'manage'])->name('manage');
        Route::get('/success', [App\Http\Controllers\SubscriptionController::class, 'success'])->name('success');
        Route::get('/{plan}', [App\Http\Controllers\SubscriptionController::class, 'show'])->name('show');
        Route::get('/{plan}/select-period', [App\Http\Controllers\SubscriptionController::class, 'selectPeriod'])->name('select-period');
        Route::post('/{plan}/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/change-plan', [App\Http\Controllers\SubscriptionController::class, 'changePlan'])->name('change-plan');
        Route::post('/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [App\Http\Controllers\SubscriptionController::class, 'resume'])->name('resume');
        Route::post('/update-counters', [App\Http\Controllers\SubscriptionController::class, 'updateResourceCounters'])->name('update-counters');
    });
});

// Подключение маршрутов для разных ролей из отдельной папки
require __DIR__.'/roles/admin.php';
require __DIR__.'/roles/client.php';
require __DIR__.'/roles/partner.php';
require __DIR__.'/roles/employee.php';

// Подключение тестовых маршрутов для отладки
require __DIR__.'/test.php';

// Отладочные роуты
Route::get('/debug/role-check', function () {
    return view('debug.role-check');
})->name('debug.role-check');

Route::get('/debug/ajax-test', function () {
    return view('debug.ajax-test');
})->name('debug.ajax-test');

Route::get('/debug/button-test', function () {
    return view('debug.button-test');
})->name('debug.button-test');

Route::get('/debug/button-visibility', function () {
    return view('debug.button-visibility-test');
})->name('debug.button-visibility');

// Веб-маршруты для модальных окон (дополнительный путь для совместимости с аутентификацией)
Route::middleware(['auth'])->prefix('projects/{projectId}')->group(function () {
    Route::get('/modals/{type}', 'App\Http\Controllers\ProjectModalController@getModal')->name('projects.modals.show');
});

// Тестовый маршрут для проверки layout
Route::get('/test-layout', function () {
    return view('test-layout');
})->name('test.layout');
