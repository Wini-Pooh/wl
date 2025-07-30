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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Система документов и электронной подписи
Route::middleware(['auth'])->group(function () {
    // Документы
    Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'show'])->name('documents.show');
    Route::post('/documents/{document}/send', [App\Http\Controllers\DocumentController::class, 'send'])->name('documents.send');
    Route::post('/documents/{document}/sign', [App\Http\Controllers\DocumentController::class, 'sign'])->name('documents.sign');
    Route::get('/documents/{document}/verify', [App\Http\Controllers\DocumentController::class, 'verifySignature'])->name('documents.verify');
    Route::get('/documents/{document}/export-signature', [App\Http\Controllers\DocumentController::class, 'exportSignature'])->name('documents.export-signature');
    Route::get('/documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // Шаблоны документов
    Route::get('/document-templates', [App\Http\Controllers\DocumentTemplateController::class, 'index'])->name('document-templates.index');
    Route::get('/document-templates/create', [App\Http\Controllers\DocumentTemplateController::class, 'create'])->name('document-templates.create');
    Route::post('/document-templates', [App\Http\Controllers\DocumentTemplateController::class, 'store'])->name('document-templates.store');
    Route::get('/document-templates/{template}', [App\Http\Controllers\DocumentTemplateController::class, 'show'])->name('document-templates.show');
    Route::get('/document-templates/{template}/edit', [App\Http\Controllers\DocumentTemplateController::class, 'edit'])->name('document-templates.edit');
    Route::put('/document-templates/{template}', [App\Http\Controllers\DocumentTemplateController::class, 'update'])->name('document-templates.update');
    Route::delete('/document-templates/{template}', [App\Http\Controllers\DocumentTemplateController::class, 'destroy'])->name('document-templates.destroy');
    Route::get('/document-templates/{template}/get', [App\Http\Controllers\DocumentTemplateController::class, 'getTemplate'])->name('document-templates.get');
});

// Подключение маршрутов для разных ролей из отдельной папки
require __DIR__.'/roles/admin.php';
require __DIR__.'/roles/client.php';
require __DIR__.'/roles/partner.php';
require __DIR__.'/roles/employee.php';

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

// Удалены маршруты системы электронных документов и подписи

// Веб-маршруты для модальных окон (дополнительный путь для совместимости с аутентификацией)
Route::middleware(['auth'])->prefix('projects/{projectId}')->group(function () {
    Route::get('/modals/{type}', 'App\Http\Controllers\ProjectModalController@getModal')->name('projects.modals.show');
});

// Удален тестовый маршрут для системы документов
