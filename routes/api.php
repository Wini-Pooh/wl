<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectModalController;
use App\Http\Controllers\Partner\ProjectPhotoController;
use App\Http\Controllers\Partner\ProjectDesignController;
use App\Http\Controllers\Partner\ProjectSchemeController;
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

// Тестовый маршрут для проверки API дизайна
Route::middleware(['auth:web'])->get('/test-design-api/{projectId}', function (Request $request, $projectId) {
    return response()->json([
        'success' => true,
        'project_id' => $projectId,
        'user' => $request->user()->email ?? 'no user',
        'message' => 'API работает'
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
Route::middleware(['auth:web', 'role:partner,employee,foreman,client,admin'])->prefix('projects/{projectId}')->group(function () {
    // Загрузка файлов
    Route::post('/photos', [ProjectPhotoController::class, 'storeByProjectId']);
    Route::post('/design', [ProjectDesignController::class, 'storeByProjectId']); // изменено с designs на design
    Route::post('/schemes', [ProjectSchemeController::class, 'storeByProjectId']);
    
    // Получение опций для фильтров дизайна (должно быть ПЕРЕД общими маршрутами design/{id})
    Route::get('/design/filter-options', [ProjectDesignController::class, 'getFilterOptionsByProjectId']);
    
    // Получение опций для фильтров схем (должно быть ПЕРЕД общими маршрутами schemes/{id})
    Route::get('/schemes/filter-options', [ProjectSchemeController::class, 'getFilterOptionsByProjectId']);
    
    // Получение файлов
    Route::get('/photos/{id}', [ProjectPhotoController::class, 'showByProjectId']);
    Route::get('/design/{id}', [ProjectDesignController::class, 'showByProjectId']); // изменено с designs на design
    Route::get('/schemes/{id}', [ProjectSchemeController::class, 'showByProjectId']);
    
    // Получение списков файлов
    Route::get('/photos', [ProjectPhotoController::class, 'indexByProjectId']);
    Route::get('/design', [ProjectDesignController::class, 'indexByProjectId']); // изменено с designs на design
    Route::get('/schemes', [ProjectSchemeController::class, 'indexByProjectId']);
    
    // Скачивание файлов
    Route::get('/photos/{id}/download', [ProjectPhotoController::class, 'downloadByProjectId']);
    Route::get('/design/{id}/download', [ProjectDesignController::class, 'downloadByProjectId']); // изменено с designs на design
    Route::get('/schemes/{id}/download', [ProjectSchemeController::class, 'downloadByProjectId']);
    
    // Удаление файлов
    Route::delete('/photos/{id}', [ProjectPhotoController::class, 'destroyByProjectId']);
    Route::delete('/design/{id}', [ProjectDesignController::class, 'destroyByProjectId']); // изменено с designs на design
    Route::delete('/schemes/{id}', [ProjectSchemeController::class, 'destroyByProjectId']);
});

// Дополнительные роуты для прямого удаления файлов по ID (для обратной совместимости)
Route::middleware(['auth:web', 'role:partner,admin'])->group(function () {
    Route::delete('/photos/{id}', [ProjectPhotoController::class, 'destroy']);
    Route::delete('/design/{id}', [ProjectDesignController::class, 'destroy']);
    Route::delete('/schemes/{id}', [ProjectSchemeController::class, 'destroy']);
});

// Маршруты API для работы с финансовыми данными проектов
Route::middleware(['auth:web', 'role:partner,employee,foreman,client,admin'])->prefix('finance-api/projects/{projectId}')->group(function () {
    // Маршруты для получения частей финансовых вкладок
    Route::get('/materials-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getMaterialsPartial']);
    Route::get('/works-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getWorksPartial']);
    Route::get('/transports-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getTransportsPartial']);
});

// ВРЕМЕННОЕ РЕШЕНИЕ: Дублирующие маршруты БЕЗ префикса api для совместимости
Route::middleware(['auth:web', 'role:partner,employee,foreman,client,admin'])->prefix('finance-api/projects/{projectId}')->group(function () {
    Route::get('/materials-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getMaterialsPartial']);
    Route::get('/works-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getWorksPartial']);
    Route::get('/transports-partial', [App\Http\Controllers\Partner\ProjectFinanceController::class, 'getTransportsPartial']);
});
