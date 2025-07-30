<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectModalController;
use App\Http\Controllers\Partner\ProjectPhotoController;
use App\Http\Controllers\Partner\ProjectDocumentController;
use App\Http\Controllers\Partner\ProjectDesignController;
use App\Http\Controllers\Partner\ProjectSchemeController;
use App\Http\Controllers\DocumentTemplateController;
use App\Models\Project;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Тестовый маршрут для проверки аутентификации
Route::middleware(['auth:web'])->get('/test-auth', function (Request $request) {
    return response()->json([
        'authenticated' => true,
        'user' => $request->user()->email ?? 'no user',
        'user_id' => $request->user()->id ?? 'no id'
    ]);
});

Route::middleware(['auth:web', 'role:partner,admin'])->get('/test-role', function (Request $request) {
    return response()->json([
        'authenticated' => true,
        'user' => $request->user()->email ?? 'no user',
        'has_role' => 'partner or admin'
    ]);
});

// Маршруты для AJAX-модальных окон проектов
// Маршруты для получения модальных окон (без аутентификации для тестирования)
Route::prefix('projects/{project}')->group(function () {
    // Получение модальных окон
    Route::get('/modals/{type}', [ProjectModalController::class, 'getModal']);
});

// Маршруты для загрузки файлов (с веб-аутентификацией)
// Поддержка как web, так и sanctum аутентификации для AJAX запросов
Route::middleware(['auth:web', 'role:partner,admin'])->prefix('projects/{projectId}')->group(function () {
    // Загрузка файлов
    Route::post('/photos', [ProjectPhotoController::class, 'storeByProjectId']);
    Route::post('/documents', [ProjectDocumentController::class, 'storeByProjectId']);
    Route::post('/design', [ProjectDesignController::class, 'storeByProjectId']); // изменено с designs на design
    Route::post('/schemes', [ProjectSchemeController::class, 'storeByProjectId']);
    
    // Получение файлов
    Route::get('/photos/{id}', [ProjectPhotoController::class, 'showByProjectId']);
    Route::get('/documents/{id}', [ProjectDocumentController::class, 'showByProjectId']);
    Route::get('/design/{id}', [ProjectDesignController::class, 'showByProjectId']); // изменено с designs на design
    Route::get('/schemes/{id}', [ProjectSchemeController::class, 'showByProjectId']);
    
    // Получение списков файлов
    Route::get('/photos', [ProjectPhotoController::class, 'indexByProjectId']);
    Route::get('/documents', [ProjectDocumentController::class, 'indexByProjectId']);
    Route::get('/design', [ProjectDesignController::class, 'indexByProjectId']); // изменено с designs на design
    Route::get('/schemes', [ProjectSchemeController::class, 'indexByProjectId']);
    
    // Скачивание файлов
    Route::get('/photos/{id}/download', [ProjectPhotoController::class, 'downloadByProjectId']);
    Route::get('/documents/{id}/download', [ProjectDocumentController::class, 'downloadByProjectId']);
    Route::get('/design/{id}/download', [ProjectDesignController::class, 'downloadByProjectId']); // изменено с designs на design
    Route::get('/schemes/{id}/download', [ProjectSchemeController::class, 'downloadByProjectId']);
    
    // Удаление файлов
    Route::delete('/photos/{id}', [ProjectPhotoController::class, 'destroyByProjectId']);
    Route::delete('/documents/{id}', [ProjectDocumentController::class, 'destroyByProjectId']);
    Route::delete('/design/{id}', [ProjectDesignController::class, 'destroyByProjectId']); // изменено с designs на design
    Route::delete('/schemes/{id}', [ProjectSchemeController::class, 'destroyByProjectId']);
});

// Дополнительные роуты для прямого удаления файлов по ID (для обратной совместимости)
Route::middleware(['auth:web', 'role:partner,admin'])->group(function () {
    Route::delete('/photos/{id}', [ProjectPhotoController::class, 'destroy']);
    Route::delete('/documents/{id}', [ProjectDocumentController::class, 'destroy']);
    Route::delete('/design/{id}', [ProjectDesignController::class, 'destroy']);
    Route::delete('/schemes/{id}', [ProjectSchemeController::class, 'destroy']);
});

// Маршруты для получения списков (для системы шаблонов)
Route::middleware(['auth:web'])->group(function () {
    // Списки для автозаполнения
    Route::get('/projects/list', [DocumentTemplateController::class, 'getProjects']);
    Route::get('/employees/list', [DocumentTemplateController::class, 'getEmployees']);
    // Данные конкретного проекта для автозаполнения
    Route::get('/projects/{projectId}/data', [\App\Http\Controllers\DocumentProjectController::class, 'getProjectData']);
});

// Маршруты для системы шаблонов документов
Route::middleware(['auth:web'])->prefix('document-templates')->group(function () {
    // Получение данных для автозаполнения
    Route::get('/employees', [DocumentTemplateController::class, 'getEmployees']);
    Route::get('/projects', [DocumentTemplateController::class, 'getProjects']);
    
    // Получение списка шаблонов и полей
    Route::get('/templates', [DocumentTemplateController::class, 'getTemplatesList']);
    Route::get('/templates/{templateType}/fields', [DocumentTemplateController::class, 'getTemplateFields']);
    Route::get('/templates/{id}/show', [DocumentTemplateController::class, 'show']);
    
    // Данные для автозаполнения
    Route::get('/projects/{projectId}/auto-fill', [DocumentTemplateController::class, 'getProjectAutoFillData']);
    Route::get('/employees/{employeeId}/auto-fill', [DocumentTemplateController::class, 'getEmployeeAutoFillData']);
    
    // Создание документа
    Route::post('/create', [DocumentTemplateController::class, 'createFromTemplate']);
});

// Маршруты для системы шаблонов документов с префиксом partner (для совместимости с фронтендом)
Route::middleware(['auth:web'])->prefix('partner/document-templates')->group(function () {
    // Получение данных для автозаполнения
    Route::get('/employees', [DocumentTemplateController::class, 'getEmployees']);
    Route::get('/projects', [DocumentTemplateController::class, 'getProjects']);
    
    // Получение списка шаблонов и полей
    Route::get('/', [DocumentTemplateController::class, 'getTemplatesList']); // Основной маршрут для получения шаблонов
    Route::get('/templates', [DocumentTemplateController::class, 'getTemplatesList']);
    Route::get('/{templateType}/fields', [DocumentTemplateController::class, 'getTemplateFields']);
    Route::get('/templates/{templateType}/fields', [DocumentTemplateController::class, 'getTemplateFields']);
    Route::get('/{id}/show', [DocumentTemplateController::class, 'show']);
    Route::get('/templates/{id}/show', [DocumentTemplateController::class, 'show']);
    
    // Данные для автозаполнения
    Route::get('/project/{projectId}/auto-fill', [DocumentTemplateController::class, 'getProjectAutoFillData']);
    Route::get('/employee/{employeeId}/auto-fill', [DocumentTemplateController::class, 'getEmployeeAutoFillData']);
    Route::get('/projects/{projectId}/auto-fill', [DocumentTemplateController::class, 'getProjectAutoFillData']);
    Route::get('/employees/{employeeId}/auto-fill', [DocumentTemplateController::class, 'getEmployeeAutoFillData']);
    
    // Создание документа
    Route::post('/create', [DocumentTemplateController::class, 'createFromTemplate']);
    Route::post('/create-from-template', [DocumentTemplateController::class, 'createFromTemplate']);
});
